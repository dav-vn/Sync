<?php

namespace Sync\Api;

use AmoCRM\Client\AmoCRMApiClient;
use Symfony\Component\Dotenv\Dotenv;


$dotenv = new Dotenv();
$dotenv->load('./.env');

/**
 * Class ApiService.
 *
 * @package Sync\Api
 */
class AmoApiService
{
    /** @var AmoCRMApiClient AmoCRM клиент. */
    protected AmoCRMApiClient $apiClient;

    /**
     * AmoApiService constructor.
     */
    public function __construct()
    {
        $this->apiClient = new AmoCRMApiClient(
            $_ENV['INTEGRATION_ID'],
            $_ENV['INTEGRATION_SECRET'],
            $_ENV['REDIRECT_URL'],
        );
    }
}


