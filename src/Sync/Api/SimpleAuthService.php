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
     * @return string | array  Имя авторизованного аккаунта | Вывод ошибки
     */
    public function auth(array $queryParams)
    {
        if (
            !empty($queryParams['code']) ||
            is_numeric($queryParams['code'])
        ) {
            return [
                'status' => 'error',
                'error_message' => 'Not a valid ID',
            ];
        }

        try {
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


