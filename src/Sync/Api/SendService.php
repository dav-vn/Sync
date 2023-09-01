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
     * @param array $queryParams
     * @return array Ответ на запрос о выгрузке контактов в Unisender.
     */
    public function sendContacts(array $queryParams): array
    {
        $this->contactService = new ContactsService();

        $contactsList = $this
            ->contactService
            ->get($queryParams);

        $this
            ->contactService
            ->save($contactsList, intval($queryParams['id']));

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

    /**
     * Отправка контакта в Unisender из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     * @return string
     */
    public function syncContacts(array $bodyParams): string
    {
        $request = [];

        if (isset($bodyParams['contacts']['update']) || isset($bodyParams['contacts']['add'])) {
            $this->contactService = new ContactsService;

            if (isset($bodyParams['contacts']['update'])) {
                $contactName = $bodyParams['contacts']['update']['name'];
            } elseif (isset($bodyParams['contacts']['add']['name'])) {
                $contactName = $bodyParams['contacts']['update']['name'];
            }

            $contactsList = $this
                ->contactService
                ->get($bodyParams['account']);

            foreach ($contactsList as $contacts) {
                foreach ($contacts as $contact) {
                    if ($contact['name'] == $contactName) {
                        $contactEmail = $contact['email'];
                        $request = array(
                            'field_names' => ['email', 'Name'],
                            'data' => [$contactName, $contactEmail],
                        );

                        Contact::updateOrCreate([
                            'amo_id' => $bodyParams['account']['id']
                        ], [
                            'email' => $contactEmail,
                            'name' => $contactName,
                        ]);

                        $request = $this
                            ->unisenderApi
                            ->importContacts($request);
                    }
                }
            }
        }

        return $request;
    }
}