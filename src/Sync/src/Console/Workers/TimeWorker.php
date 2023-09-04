<?php

namespace Sync\src\Console\Workers;

use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Throwable;

/**
 * Class TimeWorker
 *
 * @package Sync\src\Console\Workers;
 */
class TimeWorker
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
        $this->connection = Pheanstalk::create('localhost');
    }

    /**
     * Обработка очереди
     *
     * @return void
     */
    public function execute()
    {
        while ($job = $this->connection->watch($this->queue)->ignore(PheanstalkInterface::DEFAULT_TUBE)->reserve()) {
            try {
                $this->process(json_decode($job->getData(), true, 512, JSON_THROW_ON_ERROR));
                $this->connection->delete($job);
            } catch (Throwable $exception) {
            }
        }
    }

    /**
     * Выполнение команды
     *
     * @param string $data
     */
    public function process(string $data)
    {
        echo "Текущее время: " . $data . PHP_EOL;
    }
}