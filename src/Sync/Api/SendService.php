<?php

namespace Sync\Api;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
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
    private AuthService $authService;

    private AmoApiService $apiClient;
    private UnisenderContactService $unisenderContactService;

    /**
     * Отправка контактов в Unisender из БД
     *
     * @param array $queryParams
     * @return string Ответ на запрос о выгрузке контактов в Unisender.
     */
    public function sendContacts(array $queryParams): string
    {
        $this->contactService = new ContactsService();

        $contactsPages = $this
            ->contactService
            ->get($queryParams);


        $result = [];
        $data = [];

        foreach ($contactsPages as $contacts) {
            foreach ($contacts as $contact) {
                if (!empty($contact['email'])) {
                    foreach ($contact['email'] as $email) {
                        $data[] = [$email, $contact['name']];
                    }
                }
            }
        }

        $request = [
            'field_names' => ['email', 'Name'],
            'data' => $data,
        ];

        $result = $this
            ->unisenderApi
            ->importContacts($request);

        if (strpos($result, 'result')) {
            $this->contactService->save($contactsPages, intval($queryParams['id']));
        }

        return $result;
    }

    /**
     * Добавление контакта в Unisender из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMApiException
     */
    public function addContact(array $bodyParams)
    {
        $data = [];
        $contactId = intval($bodyParams['contacts']['add'][0]['id']);
        $userId = intval($bodyParams['account']['id']);

        $this->contactService = new ContactsService;
        $contacts = $this->contactService->getOne($contactId, $userId);

        $emails = $this->contactService->getEmails($contacts);
        $name = $bodyParams['contacts']['add'][0]['name'];

        foreach ($emails as $email) {
            $data[] = [$email, $name];
        }

        $request = [
            'field_names' => ['email', 'Name'],
            'data' => $data,
        ];

        $result = $this
            ->unisenderApi
            ->importContacts($request);

        if (strpos($result, 'result')) {
            $this->saveChanges($contactId, $request['data'], $userId);
        }
    }

    /**
     * Обновление контакта в Unisender из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMApiException
     */
    public function updateContact(array $bodyParams)
    {
        $data = [];
        $contactId = intval($bodyParams['contacts']['update'][0]['id']);
        $userId = intval($bodyParams['account']['id']);

        $this->contactService = new ContactsService;
        $contacts = $this->contactService->getOne($contactId, $userId);

        $emails = $this->contactService->getEmails($contacts);
        $name = $bodyParams['contacts']['update'][0]['name'];

        foreach ($emails as $email) {
            $data[] = [$email, $name];
        }

        $request = [
            'field_names' => ['email', 'Name'],
            'data' => $data,
        ];

        $result = $this
            ->unisenderApi
            ->importContacts($request);

        if (strpos($result, 'result')) {
            $this->saveChanges($contactId, $request['data'], $userId);
        }
    }

    /**
     * Удаление контакта в Unisender  из всех списков из БД по сигналу от вебхука
     *
     * @param array $bodyParams
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMApiException
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
            $name = $contact['name'];
            if ($contact['id'] == $contactId) {
                $emails = $contact['email'];
            }
        }

        foreach ($emails as $email) {
            $data[] = [$email, $name];

            $request = [
                'field_names' => ['email', 'Name', 'delete'],
                'data' => $data,
            ];

            $this
                ->unisenderApi
                ->importContacts($request);

            if (!empty($result)) {
                Contact::where('email', $email)->delete();
            }
        }
    }

    /**
     * Cохранение изменений в БД по сигналу от вебхука
     *
     * @param int $contactId
     * @param array $data
     * @param int $userId
     */
    public function saveChanges(int $contactId, array $data, int $userId): void
    {
        for ($i = 0; $i < count($data); $i++) {
            Contact::updateOrCreate([
                'contact_id' => $contactId,
            ], [
                'name' => $data[$i][0],
                'email' => $data[$i][1],
                'amo_id' => $userId
            ]);
        }
    }
}