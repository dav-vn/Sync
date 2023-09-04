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
    /**
     * Получение токена досутпа для аккаунта при наличии кода авторизации
     *
     * @param array $queryParams Входные GET параметры.
     * @return array  Имя авторизованного аккаунта | Вывод ошибки
     */
    public function auth(array $queryParams)
    {
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
}


