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
        $queryParams = $request->getQueryParams();

        if ($queryParams['id'] == 0 || !is_numeric($queryParams['id']) || empty($queryParams['id'])) {
            return new JsonResponse([
                'status' => 'error',
                'error_message' => 'ID Validation error',
            ], 400);
        }
        return new JsonResponse([
            'status' => 'success',
            'data' => $apiService->get($queryParams),
        ], 200);
    }
}


