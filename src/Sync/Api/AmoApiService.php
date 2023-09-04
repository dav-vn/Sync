<?php

namespace Sync\Api;

use AmoCRM\Client\AmoCRMApiClient;
use Sync\Models\Integration;

/**
 * Class ApiService.
 *
 * @package Sync\Api
 */
class AmoApiService
{
    /** @var AmoCRMApiClient AmoCRM клиент. */
    protected AmoCRMApiClient $apiClient;

    /** @var DatabaseConnectService Подключение к базе данных */
    protected DatabaseConnectService $connectDB;

    /**
     * AmoApiService constructor.
     */
    public function __construct()
    {
        $this->connectDB = new DatabaseConnectService();
        $integration = Integration::find(1);

        $this->apiClient = new AmoCRMApiClient(
            $integration->integration_id,
            $integration->integration_secret,
            $integration->redirect_url
        );
    }

}



