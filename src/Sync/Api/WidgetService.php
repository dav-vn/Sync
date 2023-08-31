<?php

namespace Sync\Api;

use Sync\Models\Access;

/**
 * Class WidgetService.
 *
 * @package Sync\Api
 */
class WidgetService
{
    /** @var DatabaseConnectService Подключение к базе данных */
    protected DatabaseConnectService $databaseConnect;

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
            return [
                'status' => 'succes',
                'added' => $result,
            ];
        } else {
            return [
                'status' => 'error',
                'error_message' => 'Couldnt add api_key'
            ];
        }
    }
}


