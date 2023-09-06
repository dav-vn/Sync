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

                for ($i = 0; $i <= intdiv($count, 250); $i++) {
                    $contactsFilter = (new ContactsFilter())
                        ->setLimit(250)
                        ->setPage($i + 1);

                    try {
                        $contacts = $this
                            ->apiClient
                            ->contacts()
                            ->get($contactsFilter)
                            ->toArray();

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

                    if (!empty($contacts)) {
                        foreach ($contacts as $contact) {
                            $name = $contact['name'];
                            $contactID = $contact['id'];
                            $emails = $this->getEmails($contact);

                            $contactPage[] = [
                                'name' => $name,
                                'email' => !empty($emails) ? $emails : null,
                                'contact_id' => $contactID,
                            ];
                        }
                        $contactsPages[] = $contactPage;
                    }
                }

                return $contactsPages;
            }

        } else {
            $this->auth($queryParams);
        }
    }


    /**
     * Получение одного контакта по параметрам ID
     *
     * @param int $contactId
     * @param int $userId
     * @return array
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMMissedTokenException
     * @throws Exception
     * @throws AmoCRMApiException
     */
    public function getOne(int $contactId, int $userId): array
    {
        try {
            $this->initialise($userId);

            return $this
                ->apiClient
                ->contacts()->getOne($contactId)->toArray();
        } catch (AmoCRMMissedTokenException $e) {
            return [
                'status' => 'error',
                'error_message' => 'Ошибка доступа к токену',
            ];
        } catch (AmoCRMoAuthApiException $e) {
            return [
                'status' => 'error',
                'error_message' => 'Ошибка доступа к API',
            ];
        } catch (AmoCRMApiException $e) {
            return [
                'status' => 'error',
                'error_message' => 'Ошибка вызова к API',
            ];
        }
    }


    /**
     * Сохранение массива контактов в БД
     *
     * @param array $contactsPages
     * @param int $userID
     * @return void
     */
    public function save(array $contactsPages, int $userID): void
    {
        foreach ($contactsPages as $contacts) {
            foreach ($contacts as $contact) {
                if (is_array($contact)) {
                    $emails = $contact['email'];
                    foreach ($emails as $email) {
                        Contact::updateOrCreate(
                            [
                                'contact_id' => $contact['contact_id'],
                                'amo_id' => $userID,
                            ],[
                                'name' => $contact['name'],
                                'email' => $email,
                            ]);
                    }
                }
            }
        }
    }

    /**
     * Поиск рабочих адресов почты и сохранение в массив
     *
     * @param array $contacts
     * @return array
     */
    public function getEmails(array $contacts): array
    {
        $emails = [];
        foreach ($contacts['custom_fields_values'] as $values) {
            if ($values['field_code'] === 'EMAIL') {
                foreach ($values['values'] as $value) {
                    $emails[] = $value['value'];
                }
            }
        }

        return $emails;
    }
}


