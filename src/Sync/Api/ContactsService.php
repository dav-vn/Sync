<?php

namespace Sync\Api;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Models\ContactModel;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sync\Entity\EntityServiceInterface;
use Throwable;
use Sync\Api\AuthService;

/**
 * Class AuthService.
 *
 * @package Sync\Api
 */
class ContactsService implements EntityServiceInterface
{

    /** @var AuthService клиент. */
    private AuthService $authService;

    /** @var AmoCRMApiClient AmoCRM клиент. */
    private AmoCRMApiClient $apiClient;

    /** @var string Файл хранения токенов. */
    private const TOKENS_FILE = './tokens.json';


    /**
     * ContactService constructor
     */
    public function __construct()
    {
        $this->authService = new AuthService();
        $this->apiClient = new AmoCRMApiClient(
            '86ce267b-409f-47b7-bd40-2190ff0a743c',
            'EkfgYsqNbys5vmfBqLs4CVzMKdWVwVT6pjvBHXMUxDljqNPgZz5M0GD6Fp5PuevI',
            'https://localhost.loca.lt/auth',
        );
    }

    /**
     * Получение JSON обьекта с данными по сущности контакт
     *
     * @param array $queryParams Входные GET параметры.
     * @return object возвращаем список из всех контактов.
     */
    public function get(array $queryParams): object
    {
        $id = array_keys(json_decode(file_get_contents(self::TOKENS_FILE), true));
        $id = intval(reset($id));


        $accessToken = $this->authService->readToken($id);

        $this->apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['base_domain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );


        return $this->apiClient->contacts()->get();
    }

    /**
     * Добавление нового контакта и получение обновленного списка контактов
     *
     * @param string $name Имя нового контакта
     * @return object возвращаем список из всех контактов.
     */
    public function add(string $name): object
    {
        $contact = new ContactModel();
        $contact->setName($name);

        try {
            $this->apiClient->contacts()->addOne($contact);
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }

        return $this->apiClient->contacts()->get();
    }

    /**
     * Добавление нового контакта и получение обновленного списка контактов
     *
     * @param array $names Имена новых контактов, которые необходимо создать
     * @return object возвращаем список из всех контактов.
     */
    public function addSome(array $names): object
    {
        $contactsCollection = new ContactsCollection();
        foreach ($names as $name) {
            $contact = new ContactModel();
            $contact->setName($name);

            $contactsCollection->add($contact);
        }
        try {
            $this->apiClient->contacts()->add($contactsCollection);
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }

        return $this->apiClient->contacts()->get();
    }

}
