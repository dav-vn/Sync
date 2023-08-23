<?php

namespace Sync\Api;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\ContactsFilter;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Class AuthService.
 *
 * @package Sync\Api
 */
class ContactsService extends AmoApiService
{
    /** @var string Файл хранения токенов. */
    private const TOKENS_FILE = './tokens.json';

    /** @var AuthService Сервис аутенфикации интеграции. */
    protected AuthService $authService;

    /**
     * Получение ассоциативного массива с именами всех контактов
     * и имеющихся у них email адресов.
     * @return array возвращаем список из всех контакто
     */
    public function get(): array
    {
        $pageData = [];
        $result = [];
        $count = 0;

        $this->authService = new AuthService();
        $userId = array_keys(json_decode(file_get_contents(self::TOKENS_FILE), true));
        $accessToken = $this
            ->authService
            ->readToken(intval(reset($userId)));

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
            $count = $this->apiClient->contacts()->get()->count();
        } catch (AmoCRMMissedTokenException|AmoCRMApiException $e) {
        }

        for ($i = 0; $i <= intdiv($count, 500); $i++) {
            $contactsFilter = (new ContactsFilter())->setLimit(500)->setPage($i + 1);
            try {
                $contactsData = $this->apiClient->contacts()->get($contactsFilter);
            } catch (AmoCRMMissedTokenException|AmoCRMoAuthApiException|AmoCRMApiException $e) {
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
