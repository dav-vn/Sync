<?php

namespace Sync\src\Console\Producers;

use Pheanstalk\Pheanstalk;
use Sync\Api\TokensRefreshService;

/**
 * Class VerifyTokenProducer
 *
 * @package Sync\src\Console\Producers;
 */
class VerifyTokenProducer
{
    /** @var Pheanstalk подключение к хосту */
    protected Pheanstalk $connection;

    /** @var string $queue */
    protected string $queue = 'accesses';

    /** @var TokensRefreshService сервис обновления токенов */
    private TokensRefreshService $tokensService;

    /**
     * Конструктор продюсера
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = Pheanstalk::create('application-beanstalkd', 11300);
        $this->tokensService = new TokensRefreshService;
    }

    /**
     * Добавление необходимых параметров в очередь
     *
     * @return void
     */
    public function produce($requiredTime)
    {
        $tokens = $this->tokensService->verifyTokensExpiration($requiredTime);
        $jsonData = json_encode($tokens);
        $this->connection->useTube($this->queue)->put($jsonData);
    }
}