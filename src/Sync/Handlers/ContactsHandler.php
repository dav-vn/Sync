<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\ContactsService;

/**
 * Class ContactHandler
 *
 * @package Sync\Handlers\
 */
class ContactsHandler implements RequestHandlerInterface
{
    /**
     * Обработка HTTP-запроса /contacts
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $apiService = new ContactsService();

        $contactsData = $apiService->get($request->getQueryParams());
        $result = [];

        if (!empty($contactsData)) {
            foreach ($contactsData as $contacts) {
                $name = $contacts->{'name'};
                $emails = [];
                foreach ($contacts->{'custom_fields_values'} as $values) {
                    if ($values->{'field_code'} === 'EMAIL') {
                        foreach ($values->{'values'} as $value) {
                            $emails[] = $value->{'value'};
                        }
                    }
                }

                $result[] = [
                    'name' => $name,
                    'emails' => !empty($emails) ? $emails : null,
                ];
            }
        }

        return new JsonResponse([
            $result
        ]);
    }
}


