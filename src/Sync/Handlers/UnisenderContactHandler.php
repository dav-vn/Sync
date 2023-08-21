<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\ContactsService;

/**
 * Class UnisenderContactHandler
 *
 * @package Sync\Handlers\
 */
class UnisenderContactHandler implements RequestHandlerInterface
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

        return new JsonResponse([
            $apiService->get(),
        ]);
    }
}


