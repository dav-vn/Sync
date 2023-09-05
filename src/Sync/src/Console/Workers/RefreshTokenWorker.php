<?php

namespace Sync\src\Console\Workers;

use Exception;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Illuminate\Contracts\Queue\Job;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\Api\TokensRefreshService;
use Sync\config\BeanstalkConfig;
use Throwable;

/**
 * Class RefreshTokenProducer
 *
 * @package Sync\src\Console\Producers;
 */
class RefreshTokenWorker extends BaseWorker
{
    /** @var Pheanstalk подключение к хосту */
    protected Pheanstalk $connection;

    /** @var string $queue */
    protected string $queue = 'tokens';

    /** @var BeanstalkConfig конфиг подключения */
    protected BeanstalkConfig $beanstalk;

    /** @var TokensRefreshService сервис обновления токенов */
    private TokensRefreshService $tokenService;

    /**
     * Конструктор RefreshTokenWorker
     *
     */
    public function __construct()
    {
        $container = require 'config/container.php';
        $this->tokenService = new TokensRefreshService;

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
     * Обработка задач
     *
     * @param array $data ID токенов, которые нуждаются в обновлении
     * @throws Exception
     */
    public function process($data)
    {
        if ($data) {
            foreach ($data as $tokenID) {
                $this->tokenService->refreshTokensExpiration(intval($tokenID));
                echo 'Обновлен:' . $tokenID . PHP_EOL;
            }
        } else {
            echo 'Nothing to refresh' . PHP_EOL;
        }
    }
}