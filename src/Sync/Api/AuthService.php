<?php

namespace Sync\Api;

use Exception;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sync\Interfaces\AuthInterface;
use Throwable;
use Sync\Models\Access;

/**
 * Class AuthService.
 *
 * @package Sync\Api
 */
class AuthService extends AmoApiService implements AuthInterface
{
    /** @var string Базовый домен авторизации. */
    private const TARGET_DOMAIN = 'kommo.com';

    /**
     * Получение токена досутпа для аккаунта.
     *
     * @param array $queryParams Входные GET параметры.
     * @return string | array  Имя авторизованного аккаунта | Вывод ошибки
     */
    public function auth(array $queryParams)
    {
        session_start();

        if (
            $queryParams['id'] == 0 ||
            !is_numeric($queryParams['id'])
        ) {
            return [
                'status' => 'error',
                'error_message' => 'Not a valid ID',
            ];
        } else {
            $accountId = intval($queryParams['id']);
        }

        if (!empty($accountId)) {
            $accessToken = Access::where('amo_id', $accountId)->first();

            if (!empty($accessToken)) {
                $accessToken = $this->readToken(intval($queryParams['id']));

                $this
                    ->apiClient
                    ->setAccessToken($accessToken)
                    ->setAccountBaseDomain($accessToken->getValues()['base_domain'])
                    ->onAccessTokenRefresh(
                        function (AccessTokenInterface $accessToken, string $baseDomain) use ($accountId) {
                            $this->saveToken(
                                $accountId,
                                [
                                    'accessToken' => $accessToken->getToken(),
                                    'refreshToken' => $accessToken->getRefreshToken(),
                                    'expires' => $accessToken->getExpires(),
                                    'baseDomain' => $baseDomain,
                                ]
                            );
                        }
                    );

                return $this
                    ->apiClient
                    ->getOAuthClient()
                    ->getResourceOwner($accessToken)
                    ->getName();
            }

        }
        elseif (empty($accountId)) {
            return [
                'status' => 'error',
                'error_message' => 'ID is unset',
            ];
        } else {
            $_SESSION['service_id'] = $queryParams['id'];

            if (isset($queryParams['referer'])) {
                $this
                    ->apiClient
                    ->setAccountBaseDomain($queryParams['referer'])
                    ->getOAuthClient()
                    ->setBaseDomain($queryParams['referer']);
            }

            try {
                if (!isset($queryParams['code'])) {
                    $state = bin2hex(random_bytes(16));
                    $_SESSION['oauth2state'] = $state;

                    if (isset($queryParams['button'])) {
                        echo $this
                            ->apiClient
                            ->getOAuthClient()
                            ->setBaseDomain(self::TARGET_DOMAIN)
                            ->getOAuthButton([
                                'title' => 'Установить интеграцию',
                                'compact' => true,
                                'class_name' => 'className',
                                'color' => 'default',
                                'error_callback' => 'handleOauthError',
                                'state' => $state,
                            ]);
                    } else {
                        $authorizationUrl = $this
                            ->apiClient
                            ->getOAuthClient()
                            ->setBaseDomain(self::TARGET_DOMAIN)
                            ->getAuthorizeUrl([
                                'state' => $state,
                                'mode' => 'post_message',
                            ]);
                        header('Location: ' . $authorizationUrl);
                    }
                    die;
                }
                elseif (
                    empty($queryParams['state']) ||
                    empty($_SESSION['oauth2state']) ||
                    ($queryParams['state'] !== $_SESSION['oauth2state'])
                ) {
                    unset($_SESSION['oauth2state']);
                    exit('Invalid state');
                }
            } catch (Throwable $e) {
                die($e->getMessage());
            }
        }
        session_abort();

        return $this
            ->apiClient
            ->getOAuthClient()
            ->getResourceOwner($accessToken)
            ->getName();
    }

    /**
     * Сохранение токена авторизации.
     *
     * @param int $serviceId Системный идентификатор аккаунта.
     * @param array $token Токен доступа Api.
     * @return void
     */
    public function saveToken(int $serviceId, array $token): void
    {
        if (!empty($token)) {
            Access::updateOrCreate(
                [
                    'amo_id' => $serviceId,
                    'base_domain' => $token['base_domain'],
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'],
                    'expires' => $token['expires'],
                ]
            );
        }
    }

    /**
     * Получение токена из базы данных
     *
     * @param int $serviceId Системный идентификатор аккаунта.
     * @return AccessToken
     */
    public function readToken(int $serviceId): AccessToken
    {
        try {
            $accessToken = Access::on()->where('amo_id', $serviceId)->first();

            if (!$accessToken) {
                throw new Exception('Access token not found.');
            }

            return new AccessToken($accessToken->toArray());
        } catch (Throwable $e) {
            exit($e->getMessage());
        }
    }
}


