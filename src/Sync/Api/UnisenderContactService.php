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
     * @return object возвращаем информацию о контакте.
     */
    public function getContact(array $queryParams)
    {
        if (empty($queryParams)) {
            return new JsonResponse([
                'error' => 'Email is empty'
            ], 400);
        }

        try {
            $contact = $this
                ->unisender
                ->getContact($queryParams);
            if (isset($contact)) {
                $contact = json_decode($contact);
                return $contact
                    ->{'result'}
                    ->{'email'};
            } else {
                return new JsonResponse([
                    'error' => 'Contact not found'
                ], 404);
            }
        } catch (Throwable $e) {
            die($e->getMessage());
        }
    }
}

