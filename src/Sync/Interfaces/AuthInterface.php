<?php

namespace Sync\Interfaces;

/**
 * Интерфейс для сервисов аутенфикаци
 * @package Sync\Interfaces
 */
interface AuthInterface
{
    /**
     * Получение токена досутпа для аккаунта
     *
     * @param array $queryParams Входные GET параметры.
     * @return string | array  Имя авторизованного аккаунта | Вывод ошибки
     */
    public function auth(array $queryParams);
}