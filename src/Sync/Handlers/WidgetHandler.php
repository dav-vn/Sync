<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\WidgetService;

/**
 * Class WidgetHandler
 *
 * @package Sync\Handlers\
 */
class WidgetHandler implements RequestHandlerInterface
{

    /**
     * Обработка HTTP-запроса /widget
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $bodyParams = $request->getParsedBody();
        $widgetService = new WidgetService();

        return new JsonResponse([
            $widgetService->addApiKey($bodyParams),
        ]);
    }
}

