<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\DatabaseConnectService;
use Sync\Api\GetAccountService;
use Sync\Api\IntegrationService;
use Sync\Models\Integration;

/**
 * Class AddIntegrationHandler
 *
 * @package Sync\Handlers\
 */
class GetAccountHandler implements RequestHandlerInterface
{
    /** @var GetAccountService Сервис получения и обработки списка аккаунтов */
    protected GetAccountService $accountService;

    /**
     * Обработка HTTP-запроса /auth
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->accountService = new GetAccountService;

        return new JsonResponse([
            $this->accountService->getAccounts(),
        ]);
    }
}

