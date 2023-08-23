<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\SendService;

/**
 * Class SendHandler
 *
 * @package Sync\Handlers\
 */
class SendHandler implements RequestHandlerInterface
{
    /**
     * Обработка HTTP-запроса /send
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sendService = new SendService();

        return new JsonResponse([
            $sendService->sendContacts(),
        ]);
    }
}