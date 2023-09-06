<?php

namespace Sync\Api;

use AmoCRM\Client\AmoCRMApiClient;
use Symfony\Component\Dotenv\Dotenv;
use Sync\Models\Integration;
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
        $this->connectDB = new DatabaseConnectService();
        $integration = Integration::find(1);
        $this->unisenderApi = new UnisenderApi($_ENV['UNISENDER_API']);
    }
}


