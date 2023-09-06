<?php

namespace Sync\Api;

use Sync\Interfaces\AuthInterface;
use Throwable;

/**
 * Class SimpleAuthService.
 *
 * @package Sync\Api
 */
class SimpleAuthService extends AmoApiService implements AuthInterface
{
    /** @var  AuthService **/
    private AuthService $authService;

    /**
     * Получение токена досутпа для аккаунта при наличии кода авторизации
     *
     * @param array $queryParams Входные GET параметры.
     * @return array  Имя авторизованного аккаунта | Вывод ошибки
     */
    public function auth(array $queryParams): array
    {
        $this->authService = new AuthService;

        try {
            $this
                ->apiClient
                ->setAccountBaseDomain($queryParams['referer'])
                ->getOAuthClient()
                ->setBaseDomain($queryParams['referer']);

            $accessToken = $this
                ->apiClient
                ->getOAuthClient()
                ->setBaseDomain($queryParams['referer'])
                ->getAccessTokenByCode($queryParams['code']);

            $this->apiClient->setAccessToken($accessToken);;

            $this
                ->authService
                ->saveToken($this->apiClient->account()->getCurrent()->getId(), [
                'base_domain' => $this->apiClient->getAccountBaseDomain(),
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
            ]);

        } catch (Throwable $e) {
           return [
             'status' => 'error',
             'error_message' => $e->getMessage(),
           ];
        }

        session_abort();

        return $this
            ->apiClient
            ->getOAuthClient()
            ->getResourceOwner($accessToken)
            ->getName();
    }
}


