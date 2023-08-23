<?php

namespace Sync\Api;

/**
 * Class SendService.
 *
 * @package Sync\Api
 */
class SendService extends UnisenderApiService
{
    /** @var $contactService ContactsService Сервис получения списка контактов */
    protected ContactsService $contactService;

    /**
     * Получение токена досутпа для аккаунта.
     * @return array Ответ на запрос о выгрузке контактов в Unisender.
     */
    public function sendContacts(): array
    {
        $this->contactService = new ContactsService();
        $contactsList = $this->contactService->get();

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

            $request = array(
                'field_names' => ['email', 'Name'],
                'data' => $data,
            );

            $result[] = $this->unisenderApi->importContacts($request);
        }

        if (!isset($result['error'])) {
            return [
                'status' => 'success',
                'message' => $result,
            ];
        } else {
            return [
                'status' => 'error',
                'error_message' => $result,
            ];
        }
    }
}