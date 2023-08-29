<?php

namespace Sync\Api;

use Sync\Models\Account;
use Sync\Models\Integration;

/**
 * Class ApiService.
 *
 * @package Sync\Api
 */
class IntegrationService
{
    protected DatabaseConnectService $databaseConnect;

    public function addIntegration(array $integrationData): array
    {
        $this->databaseConnect = new DatabaseConnectService;

        $account = Account::updateOrCreate([
            'amo_id' => $integrationData['amo_id'],
        ]);

        $integration = Integration::updateOrCreate([
            'integration_id' => $integrationData['integration_id'],
            'integration_secret' => $integrationData['integration_secret'],
            'redirect_url' => $integrationData['redirect_url'],
        ]);

        $account->integrations()->attach($integration);
        $result = Integration::where('integration_id', $integrationData)->first();

        if (!empty($result)) {
            return [
                'status' => 'succes',
                'added' => $result,
            ];
        } else {
            return [
                'status' => 'error',
                'error_message' => 'Couldnt add integration'
            ];
        }
    }
}



