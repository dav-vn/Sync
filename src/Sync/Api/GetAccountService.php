<?php

namespace Sync\Api;

use Sync\Models\Account;

/**
 * Class GetAccountService.
 *
 * @package Sync\Api
 */
class GetAccountService
{
    /** @var DatabaseConnectService Подключение к базе данных */
    protected DatabaseConnectService $databaseConnect;

    public function getAccounts(): array
    {
        $this->databaseConnect = new DatabaseConnectService;

        $accountsData = Account::with(['integrations', 'contacts', 'accesses'])->get()->toArray();

        if(empty($accountsData)) {
            return [
                'status' => 'error',
                'error_message' => 'Couldt find accounts',
            ];
        }

        $formattedAccounts = [
            'status' => 'success',
            'data' => [
                'accounts' => [
                    'all' => [],
                    'with_accesses' => [],
                ]
            ]
        ];

        foreach ($accountsData as $accountData) {
            $accountName = $accountData['name'];

            $integrations = [];
            foreach ($accountData['integrations'] as $integration) {
                $integrations[] = $integration['integration_id'];
            }

            $contactsCount = count($accountData['contacts']);

            $apiKey = $accountData['accesses']['api_key'];

            $accountInfo = [
                'amo_id' => $accountData['amo_id'],
                'integration_id' => $integrations,
                'contacts_count' => $contactsCount,
                'api_key' => $apiKey,
            ];

            $formattedAccounts['data']['accounts']['all'][] = [
                $accountName => $accountInfo,
            ];

            if (!empty($accountData['accesses'])) {
                $formattedAccounts['data']['accounts']['with_accesses'][] = $accountName;
            }
        }

        return $formattedAccounts;
    }
}



