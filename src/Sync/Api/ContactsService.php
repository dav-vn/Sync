<?php

namespace Sync\Api;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\ContactsFilter;
use Laminas\Diactoros\Response\JsonResponse;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Class SendService.
 *
 * @package Sync\Api
 */
class ContactsService extends AmoApiService
{
    /** @var AuthService Сервис аутенфикации интеграции. */
    protected AuthService $authService;

    /**
     * Получение ассоциативного массива с именами всех контактов
     * и имеющихся у них email адресов.
     * @param string $userId
     * @return array | object возвращаем список из всех контактов | вывод ошибки в формате JSON
     */
    public function get(string $userId)
    {
        $pageData = [];
        $result = [];
        $count = 0;

        $this->authService = new AuthService();
        $accessToken = $this
            ->authService
            ->readToken(intval($userId));

        $this
            ->apiClient
            ->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['base_domain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );

        try {
            $count = $this
                ->apiClient
                ->contacts()
                ->get()
                ->count();
        } catch (AmoCRMMissedTokenException $e) {
            return new JsonResponse([
                'status' => 'error',
                'error_message' => 'Ошибка доступа к токену',
            ]);
        } catch (AmoCRMoAuthApiException $e) {
            return new JsonResponse([
                'status' => 'error',
                'error_message' => 'Ошибка доступа к API',
            ]);
        } catch (AmoCRMApiException $e) {
            return new JsonResponse([
                'status' => 'error',
                'error_message' => 'Ошибка вызова к API',
            ]);
        }

        for ($i = 0; $i <= intdiv($count, 500); $i++) {
            $contactsFilter = (new ContactsFilter())
                ->setLimit(500)
                ->setPage($i + 1);

            try {
                $contactsData = $this
                    ->apiClient
                    ->contacts()
                    ->get($contactsFilter);
            } catch (AmoCRMMissedTokenException $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'error_message' => 'Ошибка доступа к токену',
                ]);
            } catch (AmoCRMoAuthApiException $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'error_message' => 'Ошибка доступа к API',
                ]);
            } catch (AmoCRMApiException $e) {
                return new JsonResponse([
                    'status' => 'error',
                    'error_message' => 'Ошибка вызова к API',
                ]);
            }


            if (!empty($contactsData)) {
                foreach ($contactsData as $contacts) {
                    $name = $contacts->{'name'};
                    $emails = [];
                    foreach ($contacts->{'custom_fields_values'} as $values) {
                        if ($values->{'field_code'} === 'EMAIL') {
                            foreach ($values->{'values'} as $value) {
                                $emails[] = $value->{'value'};
                            }
                        }
                    }

                    $pageData[] = [
                        'name' => $name,
                        'email' => !empty($emails) ? $emails : null,
                    ];
                }

                $result[] = $pageData;
            }
        }

        return $result;
    }
}
