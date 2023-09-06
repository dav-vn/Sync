<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Api\SendService;
use Sync\Models\Access;
use Sync\Models\Account;

/**
 * Class SendHandler
 *
 * @package Sync\Handlers\
 */
class SendHandler implements RequestHandlerInterface
{
    /**
     * Обработка HTTP-запроса /send
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sendService = new SendService();
        $queryParams = $request->getQueryParams();
        $hasAccess = Account::where('amo_id', intval($queryParams['id']))->first();

        if(!$hasAccess) {
            return new JsonResponse([
                'status' => 'error',
                'error_message' => 'No access (go to /auth)',
            ], 400);
        }

        if ($queryParams['id'] == 0 || !is_numeric($queryParams['id']) || empty($queryParams['id'])) {
            return new JsonResponse([
                'status' => 'error',
                'error_message' => 'ID Validation error',
            ], 400);
        }
        return new JsonResponse([
            'status' => 'success',
            'data' => $sendService->sendContacts($request->getQueryParams()),
        ], 200);
    }
}



