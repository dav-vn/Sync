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

        $this->contactService->save($contactsList, intval($queryParams['id']));

        $result = [];
        $data = [];

        foreach ($contactsList as $contact) {
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

        $result[] = $this
            ->unisenderApi
            ->importContacts($request);

        return $request['data'];
    }

    /**
     * Обновление контакта в Unisender из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     *
     */
    public function updateContacts(array $bodyParams)
    {
        $contactId = intval($bodyParams['contacts']['update'][0]['id']);
        $userId = intval($bodyParams['account']['id']);

        $this->contactService = new ContactsService;
        $this->contactService->getOne($contactId, $userId);

        $email = $bodyParams['contacts']['update'][0]['custom_fields'][0]['values'][0]['value'];
        $name = $bodyParams['contacts']['update'][0]['name'];

        $request = array(
            'field_names' => ['email', 'Name'],
            'data' => [[$email, $name]],
        );

        $result = $this
            ->unisenderApi
            ->importContacts($request);

        if (strpos($result, 'result')) {
            $this->saveChanges($contactId, $request['data'], $userId);
        }

        return $result;
    }

    /**
     * Добавление контакта в Unisender из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     *
     */
    public function addContact(array $bodyParams)
    {
        $contactId = intval($bodyParams['contacts']['add'][0]['id']);
        $userId = intval($bodyParams['account']['id']);

        $this->contactService = new ContactsService;
        $this->contactService->getOne($contactId, $userId);

        $email = $bodyParams['contacts']['add'][0]['custom_fields'][0]['values'][0]['value'];
        $name = $bodyParams['contacts']['add'][0]['name'];

        $request = array(
            'field_names' => ['email', 'Name'],
            'data' => [[$email, $name]],
        );

        $result = $this
            ->unisenderApi
            ->importContacts($request);

        if (strpos($result, 'result')) {
            $this->saveChanges(
                $contactId,
                $request['data'],
                $userId
            );
        }

        return $result;
    }

    /**
     * Удаление контакта в Unisender  из всех списков из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     *
     */
    public function deleteContact(array $bodyParams)
    {
        $decodedContacts = json_decode($bodyParams['contacts'], true);
        $contactId = intval($decodedContacts['update'][0]['id']);
        $decodedAccount = json_decode($bodyParams['account'], true);
        $userId = intval($decodedAccount['id']);

        $emails = [];

        $this->contactService = new ContactsService;
        $this->unisenderContactService = new UnisenderContactService;

        $contactData = $this->contactService->get($decodedAccount);

        foreach ($contactData as $contact) {
            if ($contact['id'] == $contactId) {
                $emails = $contact['email'];
                break;
            }
        }

        foreach ($emails as $email) {
            $params = array(
                'contact_type' => 'email',
                'contact' => $email,
            );

            $result = $this
                ->unisenderApi
                ->exclude($params);

            if (empty($result)) {
                Contact::where('email', $email)->delete();
            }
        }
    }


    public function saveChanges(int $contactId, array $data, $userId): void
    {
        for ($i = 0; $i < count($data); $i++) {
            Contact::updateOrCreate([
                'contact_id' => $contactId,
            ], [
                'name' => $data['name'],
                'email' => $data[$i]['email'],
                'amo_id' => $userId
            ]);
        }
    }
}