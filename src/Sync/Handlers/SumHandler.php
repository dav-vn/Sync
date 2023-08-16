<?php

declare(strict_types=1);

namespace Sync\Handlers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * Class SumHandler
 *
 * @package Sync\Handlers\
 */
class SumHandler implements RequestHandlerInterface
{
    /** @var null $log Переменная для хранения логов */
    protected $logParams = null;

    /**
     * Monolog logger constructor.
     */
    public function logger()
    {
        $currentDate = date('Y-m-d');
        $this->logParams = new Logger('sum_logger');
        $this->logParams->pushHandler(new StreamHandler("./logs/{$currentDate}/requests.log", Logger::DEBUG));
        $this->logParams->pushHandler(new FirePHPHandler);
    }

    /**
     * Обработка HTTP-запроса /sum
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $sum = 0;

        $queryParams = $request->getQueryParams();
        foreach ($queryParams as $key => $value) {
            if (is_numeric($key) && is_numeric($value) === false) {
                $sum += intval($key);
            } elseif ((is_numeric($key) && is_numeric($value))) {
                $sum += intval($value);
            } elseif (is_numeric($key) === false && is_numeric($value)) {
                $sum += intval($value);
            }
        }

        $this->logger();
        $this->logParams->info($sum);

        return new JsonResponse([
            'sum' => $sum,
        ]);
    }
}