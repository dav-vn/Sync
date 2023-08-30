<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\AuthService;
use Sync\Api\SimpleAuthService;

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
        $auth = new AuthService();
        $simpleAuth = new SimpleAuthService();

        $accountParams = $request->getQueryParams();

        if (isset($accountParams['code'])) {
            $result = $simpleAuth->auth($accountParams);
        } else {
            $result = $auth->auth($accountParams);
        }

        return new JsonResponse([
            $result
        ]);
    }
}

