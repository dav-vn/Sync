<?php

namespace Sync\Api;

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load('./.env');

/**
 * Class UnisenderContactService.
 *
 * @package Sync\Api
 */
class UnisenderContactService extends UnisenderApiService
{
    /**
     * Получение информации о контакте в Unisender по привязанной почте
     * @param array $queryParams Входные GET параметры.
     * @return object | string[] JSON данные о контакте | Сообщение об ошибке
     **/
    public function getContact(array $queryParams)
    {
        if (empty($queryParams['email'])) {
            return [
                'status' => 'error',
                'error_message' => 'Email is empty',
            ];
        }

        $contact = $this
            ->unisenderApi
            ->getContact($queryParams);

        if (strpos($contact, 'result')) {
            return json_decode($contact)
                ->{'result'}
                ->{'email'};
        } elseif (strpos($contact, 'not a valid email address')) {
            return [
                'status' => 'error',
                'error_message' => 'Email not found'
            ];
        }
    }
}


