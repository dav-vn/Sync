<?php

declare(strict_types=1);

namespace Sync\Handlers;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\ApiService;

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
        $apiService = new ApiService();

        $queryParams = $request->getQueryParams();

        $apiService->auth($queryParams);



        return new JsonResponse([
        ]);
    }


}

