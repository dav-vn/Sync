<?php

namespace Sync\src\Console\Producers;

use Pheanstalk\Pheanstalk;

/**
 * Class TimeProducer
 *
 * @package Sync\src\Console\Producers;
 */
class TimeProducer
{
    /** @var Pheanstalk подключение к хосту */
    protected Pheanstalk $connection;

    /** @var string $queue */
    protected string $queue = 'times';

    /**
     * Конструктор воркера
     *
     * @return void
     */
    public function __construct()
    {
        $this->connection = Pheanstalk::create('localhost', 11300);
    }

    /**
     * Добавление необходимых параметров в очередь
     *
     * @return void
     */
    public function produce($data)
    {
        $jsonData = json_encode($data);
        $this->connection->useTube($this->queue)->put($jsonData);
    }
}