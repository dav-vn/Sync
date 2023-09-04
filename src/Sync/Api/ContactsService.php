<?php

namespace Sync\Api;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Exceptions\BadTypeException;
use AmoCRM\Filters\ContactsFilter;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Sync\Models\Access;
use Sync\Models\Contact;

/**
 * Class ContactsService.
 *
 * @package Sync\Api
 */
class ContactsService extends AuthService
{
    /** @var AuthService Сервис аутенфикации интеграции. */
    protected AuthService $authService;


    /**
     * Получение ассоциативного массива с именами всех контактов
     * и имеющихся у них email адресов.
     *
     * @param array $queryParams
     * @return array | object возвращаем список из всех контактов | вывод ошибки в формате JSON
     * @throws BadTypeException
     * @throws Exception
     */
    public function get(array $queryParams)
    {
        $userID = intval($queryParams['id']);

        $this->authService = new AuthService();
        $hasToken = Access::where('amo_id', $userID)->first();

        if (!empty($hasToken)) {
            $accessToken = $this->initialise($userID);
            if (!$accessToken->hasExpired()) {
                try {
                    $count = $this
                        ->apiClient
                        ->contacts()
                        ->get()
                        ->count();
                } catch (AmoCRMMissedTokenException $e) {
                    return new JsonResponse([
                        'status' => 'error',
                        'error_message' => 'Ошибка доступа к токену',
                    ]);
                } catch (AmoCRMoAuthApiException $e) {
                    return new JsonResponse([
                        'status' => 'error',
                        'error_message' => 'Ошибка доступа к API',
                    ]);
                } catch (AmoCRMApiException $e) {
                    return new JsonResponse([
                        'status' => 'error',
                        'error_message' => 'Ошибка вызова к API',
                    ]);
                }

                for ($i = 1; $i <= intdiv($count, 250) + 1; $i++) {
                    $contactsFilter = (new ContactsFilter())
                        ->setLimit(250)
                        ->setPage($i);

                    try {
                        $contacts = $this->apiClient->contacts()->get($contactsFilter);

                        foreach ($contacts as $contact) {
                            $name = $contact->name;
                            $contactId = $contact->id;
                            $emails = [];

                            foreach ($contact->custom_fields_values as $values) {
                                if ($values->field_code === 'EMAIL') {
                                    foreach ($values->values as $currentValue) {
                                        if ($currentValue->enum === 'WORK') {
                                            $emails[] = $currentValue->value;
                                        }
                                    }
                                }
                            }

                            $contactData = [
                                'name' => $name,
                                'email' => !empty($emails) ? $emails : null,
                                'id' => $contactId,
                            ];

                            $contactsList[] = $contactData;
                        }
                    } catch (AmoCRMMissedTokenException $e) {
                        return new JsonResponse([
                            'status' => 'error',
                            'error_message' => 'Ошибка доступа к токену',
                        ], 500);
                    } catch (AmoCRMoAuthApiException $e) {
                        return new JsonResponse([
                            'status' => 'error',
                            'error_message' => 'Ошибка доступа к API',
                        ], 500);
                    } catch (AmoCRMApiException $e) {
                        return new JsonResponse([
                            'status' => 'error',
                            'error_message' => 'Ошибка вызова API',
                        ], 500);
                    }
                }
            }
            return $contactsList;
        } else {
            $this->auth($queryParams);
        }
    }


    public function getOne($contactId, $userId)
    {
        $this->initialise($userId);

//        $this->initialise($userId);

        return $this
            ->apiClient
            ->contacts()->getOne($contactId)->toArray();
    }


    /**
     * Сохранение массива контактов в БД
     *
     * @param array $contacts
     * @param int $userId
     * @return void
     */
    public function save(array $contacts, int $userId): void
    {
        foreach ($contacts as $contact) {
            if (is_array($contact)) {
                $emails = $contact['email'];
                foreach ($emails as $email) {
                    Contact::updateOrCreate(
                        ['contact_id' => $contact['id']],
                        ['name' => $contact['name'],
                            'email' => $email,
                        ]);
                }
            }
        }
    }
}


