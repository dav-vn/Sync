<?php

namespace Sync\Api;

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
     * @return array возвращаем список из всех контактов
     */
    public function get(): array
    {
        $this->authService = new AuthService();
        $userId = array_keys(json_decode(file_get_contents(self::TOKENS_FILE), true));
        $accessToken = $this->authService->readToken(intval(reset($userId)));

        $this->apiClient
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

        $contactsData = $this->apiClient->contacts()->get();
        $result = [];

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

                $result[] = [
                    'name' => $name,
                    'emails' => !empty($emails) ? $emails : null,
                ];
            }
        }

        return $result;

    }
}
