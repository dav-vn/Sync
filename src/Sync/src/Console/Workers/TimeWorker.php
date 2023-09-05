<?php

namespace Sync\src\Console\Workers;

use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Illuminate\Contracts\Queue\Job;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\config\BeanstalkConfig;
use Throwable;

/**
 * Class TimeWorker
 *
 * @package Sync\src\Console\Workers;
 */
class TimeWorker extends BaseWorker
{
    /** @var Pheanstalk подключение к хосту */
    protected Pheanstalk $connection;

    /** @var string $queue */
    protected string $queue = 'times';

    /** @var BeanstalkConfig конфиг подключения */
    protected BeanstalkConfig $beanstalk;

    /**
     * Конструктор TimeWorker
     *
     */
    public function __construct()
    {
        $container = require 'config/container.php';

        $this->beanstalk = new BeanstalkConfig($container);
        parent::__construct($this->beanstalk);
        $this->connection = $this->beanstalk->getConnection();
    }

    /**
     * Обработка очереди
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        while (
            $job = $this
                ->connection
                ->watchOnly($this->queue)
                ->ignore(PheanstalkInterface::DEFAULT_TUBE)
                ->reserve()
        ) {
            {
                try {
                    $this->process(
                        json_decode(
                            $job->getData(),
                            true,
                            512,
                            JSON_THROW_ON_ERROR
                        )
                    );
                } catch (Throwable $exception) {
                    $this->handleException($exception, $job);
                }

                $this->connection->delete($job);
            }
        }
    }

    /**
     * Обработка задачи
     * @param  $data
     */
    public function process($data)
    {
        echo "Текущее время: " . $data . PHP_EOL;
    }
}