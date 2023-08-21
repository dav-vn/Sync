<?php

namespace Sync\Api;

use Laminas\Diactoros\Response\JsonResponse;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\Dotenv\Dotenv;
use Unisender\ApiWrapper\UnisenderApi;
use Throwable;

$dotenv = new Dotenv();
$dotenv->load('./.env');

/**
 * Class UnisenderContactService.
 *
 * @package Sync\Api
 */
class UnisenderContactService
{
    /** @var UnisenderApi UnisenderApi клиент. */
    private UnisenderApi $unisender;

    /**
     * UnisenderContactService constructor.
     */
    public function __construct()
    {
        $this->unisender = new UnisenderApi($_ENV['UNISENDER_API']);
    }

    /**
     * Получение информации о контакте в Unisender по привязанной почте
     * @param array $queryParams Входные GET параметры.
     * @return object | string[] JSON данные о контакте | Сообщение об ошибке
     **/
    public function getContact(array $queryParams)
    {
        if (empty($queryParams['email'])) {
            return [
                'error' => 'Email is empty'
            ];
        }

        try {
            $contact = $this
                ->unisender
                ->getContact($queryParams);
            if (strpos($contact, 'result')) {
                return json_decode($contact)
                    ->{'result'}
                    ->{'email'};
            } elseif (strpos($contact, 'not a valid email address')) {
                return [
                    'error' => 'Email not found'
                ];
            }
        } catch (Throwable $e) {
            die($e->getMessage());
        }
    }
}


