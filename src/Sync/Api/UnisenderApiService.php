<?php

namespace Sync\Api;

use Symfony\Component\Dotenv\Dotenv;
use Unisender\ApiWrapper\UnisenderApi;

$dotenv = new Dotenv();
$dotenv->load('./.env');

/**
 * Class UnisenderApiService.
 *
 * @package Sync\Api
 */
class UnisenderApiService
{
    /** @var $unisenderApi UnisenderApi клиент */
    protected UnisenderApi $unisenderApi;

    /**
     * AmoApiService constructor.
     */
    public function __construct()
    {
        $this->unisenderApi = new UnisenderApi($_ENV['UNISENDER_API']);
    }
}


