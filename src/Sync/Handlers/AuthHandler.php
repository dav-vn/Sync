<?php

declare(strict_types=1);

namespace Sync\Handlers;

use AmoCRM\Exceptions\BadTypeException;
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
     * @throws BadTypeException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $auth = new AuthService();
        $simpleAuth = new SimpleAuthService();
        $accountParams = $request->getQueryParams();

        if (
            isset($accountParams['id'])
            &&
            strlen($accountParams['id']) < 10
        ) {
            if (
                !is_numeric($accountParams['id']) ||
                empty($accountParams['id'])
            ) {
                return new JsonResponse([
                    'status' => 'error',
                    'error_message' => 'ID Validation error',
                ], 400);
            } else {
                return new JsonResponse([
                    $auth->auth($accountParams),
                ]);
            }
        }

        if (isset($accountParams['code'])) {
            if (
                empty($accountParams['code']) ||
                is_numeric($accountParams['code']) ||
                empty($accountParams['referer']) ||
                is_numeric($accountParams['referer'])
            ) {
                return new JsonResponse([
                    'status' => 'error',
                    'error_message' => 'Invalid request',
                ], 400);
            }
            if (isset($accountParams['from_widget'])) {
                return new JsonResponse([
                    $simpleAuth->auth($accountParams),
                ]);
            } else {
                return new JsonResponse([
                    $auth->auth($accountParams),
                ]);
            }
        }

        return new JsonResponse([
            'status' => 'error',
            'error_message' => 'Invalid request',
        ], 400);
    }
}

