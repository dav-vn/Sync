<?php

namespace Sync\Api;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Models\WebhookModel;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sync\Models\Access;

/**
 * Class WidgetService.
 *
 * @package Sync\Api
 */
class WidgetService extends AmoApiService
{
    /** @var DatabaseConnectService Подключение к базе данных */
    protected DatabaseConnectService $databaseConnect;

    /** @var AuthService Сервис аутенфикации */
    private AuthService $authService;

    /**
     * Получение токена досутпа для аккаунта при наличии кода авторизации
     *
     * @param array $bodyParams Входные POST параметры.
     * @return array  Вывод строчки в БД с добавленным api_key | Вывод ошибки
     */
    public function addApiKey(array $bodyParams): array
    {
        $this->databaseConnect = new DatabaseConnectService;

        Access::updateOrCreate([
            'amo_id' => $bodyParams['account_id'],
        ], [
            'api_key' => $bodyParams['unisender_key'],
        ]);

        $result = Access::where('api_key', $bodyParams['unisender_key'])->first();

        if (!empty($result)) {
            try {
                $this->subscribe($bodyParams['account_id']);
            } catch (
            AmoCRMMissedTokenException
            |AmoCRMoAuthApiException
            |AmoCRMApiException $e
            ) {
                return [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }

            return [
                'status' => 'success',
                'added' => $result['api_key'],
            ];
        } else {
            return [
                'status' => 'error',
                'error_message' => 'Couldnt add api_key',
            ];
        }
    }


    /** Подписка на вебхук для отслеживания
     * изменения или обноволения списка контактов
     *
     * @param string $userId
     * @return void
     *
     * @throws AmoCRMApiException
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMMissedTokenException
     */
    public function subscribe(string $userId): void
    {
        $this->authService = new AuthService;

        $accessToken = $this
            ->authService
            ->readToken(intval($userId));

        $this
            ->apiClient
            ->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['base_domain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) use ($userId) {
                    $this->authService->saveToken(
                        $userId,
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );

        $webHookModel = (new WebhookModel())
            ->setSettings([
                'add_contact',
                'update_contact',
            ])
            ->setDestination('https://davvrtn.loca.lt/webhook');

        $this
            ->apiClient
            ->webhooks()
            ->subscribe($webHookModel)
            ->toArray();
    }
}


