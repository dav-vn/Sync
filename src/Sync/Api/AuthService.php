<?php

namespace Sync\Api;

use Exception;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Throwable;

session_start();

/**
 * Class AuthService.
 *
 * @package Sync\Api
 */
class AuthService extends AmoApiService
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
        $accountId = $queryParams['id'];

        if ($accountId == 0 || !is_numeric($accountId) || empty($accountId)) {
            return [
                'status' => 'error',
                'error_message' => 'Not a valid ID'
            ];
        }

        if (strpos(file_get_contents($_ENV['TOKENS_PATH']), $accountId)) {
            $accessToken = $this->readToken(intval($accountId));

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
            } elseif (
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

        try {
            $accessToken = $this
                ->apiClient
                ->getOAuthClient()
                ->setBaseDomain($queryParams['referer'])
                ->getAccessTokenByCode($queryParams['code']);

            if (!$accessToken->hasExpired()) {
                $this->saveToken($_SESSION['service_id'], [
                    'base_domain' => $this->apiClient->getAccountBaseDomain(),
                    'access_token' => $accessToken->getToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                ]);
            }
        } catch (Throwable $e) {
            die($e->getMessage());
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
        $tokens = file_exists($_ENV['TOKENS_PATH'])
            ? json_decode(file_get_contents($_ENV['TOKENS_PATH']), true)
            : [];
        $tokens[$serviceId] = $token;
        file_put_contents($_ENV['TOKENS_PATH'], json_encode($tokens, JSON_PRETTY_PRINT));
    }

    /**
     * Получение токена из файла.
     *
     * @param int $serviceId Системный идентификатор аккаунта.
     * @return AccessToken
     */
    public function readToken(int $serviceId): AccessToken
    {
        try {
            if (!file_exists($_ENV['TOKENS_PATH'])) {
                throw new Exception('Tokens file not found.');
            }

            $accesses = json_decode(file_get_contents($_ENV['TOKENS_PATH']), true);
            if (empty($accesses[$serviceId])) {
                throw new Exception();
            }

            return new AccessToken($accesses[$serviceId]);
        } catch (Throwable $e) {
            exit($e->getMessage());
        }
    }
}


