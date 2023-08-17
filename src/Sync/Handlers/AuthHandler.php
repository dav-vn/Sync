<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\AuthService;

/**
 * Class AuthHandler
 *
 * @package Sync\Handlers\
 */
class AuthHandler implements RequestHandlerInterface
{

    /**
     * Обработка HTTP-запроса /auth
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        $apiService = new AuthService();

        $username = $apiService->auth($request->getQueryParams());

        return new JsonResponse([
            'name' => $username,
        ]);

    }
}

