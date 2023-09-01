<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\SendService;

/**
 * Class WebHookHandler
 *
 * @package Sync\Handlers\
 */
class WebhookHandler implements RequestHandlerInterface
{
    /**
     * Обработка HTTP-запроса /webhook
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $bodyParams = $request->getParsedBody();
        $sendService = new SendService();

        return new JsonResponse([
            $sendService->syncContacts($bodyParams),
        ]);
    }
}

