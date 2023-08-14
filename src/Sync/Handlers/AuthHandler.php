<?php

declare(strict_types=1);

namespace Sync\Handlers;


use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class AuthHandler implements RequestHandlerInterface
{


    public function handle(ServerRequestInterface $request): ResponseInterface {

        $sum = 0;

        return new JsonResponse([
            'sum' => $sum,
        ]);

    }
}