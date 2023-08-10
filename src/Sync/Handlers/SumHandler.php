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


class SumHandler implements RequestHandlerInterface
{

    protected $log;


    public function logger() {
        $currentDate = date('Y-m-d');
        $this->log = new Logger('sum_logger');
        $this->log->pushHandler(new StreamHandler("./logs/{$currentDate}/requests.log", Logger::DEBUG));
        $this->log->pushHandler(new FirePHPHandler);

    }
    public function handle(ServerRequestInterface $request): ResponseInterface {

        $sum = 0;

        $params = $request->getQueryParams();
        foreach($params as $key => $value) {
            if(is_numeric($key) && is_numeric($value) === false) {
                $sum += intval($key);
            } elseif ((is_numeric($key) && is_numeric($value))) {
                $sum += intval($value);
            } elseif (is_numeric($key) === false && is_numeric($value))  {
               $sum += intval($value);
            }

        }

        $this->logger();
        $this->log->info($sum);

        return new JsonResponse([
            'sum' => $sum,
        ]);

    }
}