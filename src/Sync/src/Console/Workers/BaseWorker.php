<?php

namespace Sync\src\Console\Workers;

use Illuminate\Contracts\Queue\Job;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\config\BeanstalkConfig;
use Throwable;

/**
 * Class BaseWorker
 * @package Sync\src\Console\Workers;
 */
abstract class BaseWorker extends Command
{
    /** @var Pheanstalk подключение к хосту */
    protected Pheanstalk $connection;

    /** @var string $queue */
    protected string $queue = 'default';

    /**
     * Конструктор BaseWorker
     * @param BeanstalkConfig $beanstalk
     */
    public function __construct(BeanstalkConfig $beanstalk)
    {
        parent::__construct();
        $this->connection = $beanstalk->getConnection();
    }

    /**
     * Вызов через CLI
     * @param InputInterface $input
     * @param OutputInterface $output
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
     * Обработка дополнительных ошибок
     * @param Throwable $exception
     * @param Job $job
     */
    public function handleException(Throwable $exception, $job)
    {
        echo 'Error Unhandled exception $exception' . PHP_EOL . $job->getData();
    }

    /**
     * Обработка задачи
     * @param  $data
     */
    abstract public function process($data);
}
