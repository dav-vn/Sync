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
        $this->sendService = new SendService();

        if (isset($bodyParams['contacts'])) {
            $contacts = $bodyParams['contacts'];

            if (isset($contacts['add'])) {
                return new JsonResponse([
                    $this->sendService->addContacts($bodyParams),
                ], 200);

            }

            if (isset($contacts['delete'])) {
                return new JsonResponse([
                    $this->sendService->deleteContact($bodyParams),
                ], 200);

            }
            if (isset($contacts['update'])) {
                return new JsonResponse([
                    $this->sendService->updateContact($bodyParams),
                ], 200);
            }
        }

        return new JsonResponse([
            'status' => 'error',
            'error_message' => 'Invalid request (missing required parameter)'
        ], 400);
    }
}


