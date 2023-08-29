<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\IntegrationService;

/**
 * Class AddIntegrationHandler
 *
 * @package Sync\Handlers\
 */
class AddIntegrationHandler implements RequestHandlerInterface
{
    /**
     * Обработка HTTP-запроса /auth
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $integrationService = new IntegrationService;

        return new JsonResponse([
            $integrationService->addIntegration($request->getParsedBody()),
        ]);
    }
}

