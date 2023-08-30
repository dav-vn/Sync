<?php

namespace Sync\Api;


/**
 * Class SendService.
 *
 * @package Sync\Api
 */
class SendService extends UnisenderApiService
{
    /** @var ContactsService Сервис получения контактов из amoCRM */
    private ContactsService $contactService;

    /**
     * Отправка контактов в Unisender из БД
     *
     * @param string $userId
     * @return array Ответ на запрос о выгрузке контактов в Unisender.
     */
    public function sendContacts(string $userId): array
    {
        $this->contactService = new ContactsService();


        $contactsList = $this
            ->contactService
            ->get($userId);

        $this->contactService->save($contactsList, intval($userId));

        $result = [];
        $data = [];

        foreach ($contactsList as $contacts) {
            foreach ($contacts as $contact) {
                if (!empty($contact['email'])) {
                    foreach ($contact['email'] as $email) {
                        $data[] = [$email, $contact['name']];
                    }
                }
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