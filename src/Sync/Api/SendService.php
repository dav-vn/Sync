<?php

namespace Sync\Api;

use Sync\Models\Contact;


/**
 * Class SendService.
 *
 * @package Sync\Api
 */
class SendService extends UnisenderApiService
{
    /** @var $databaseConnect DatabaseConnectService Подключение к базе данных */
    private DatabaseConnectService $databaseConnect;

    /**
     * Отправка контактов в Unisender из БД
     *
     * @param string $userId
     * @return array Ответ на запрос о выгрузке контактов в Unisender.
     */
    public function sendContacts(string $userId): array
    {
        $this->databaseConnect = new DatabaseConnectService;
        $contacts = Contact::where('amo_id', intval($userId))
            ->get()
            ->toArray();

        $result = [];
        $data = [];

        foreach ($contacts as $contact) {
            if (!empty($contact['email'])) {
                $data[] = [$contact['email'], $contact['name']];
            }
        }

        $request = array(
            'field_names' => ['email', 'Name'],
            'data' => $data,
        );

        $result[] = $this
            ->unisenderApi
            ->importContacts($request);

        return $result;
    }
}