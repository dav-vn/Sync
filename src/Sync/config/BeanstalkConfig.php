<?php

namespace Sync\config;

use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BeanstalkConfig
{
    /** @var ?Pheanstalk */
    private ?Pheanstalk $connection;

    /**
     * Конструктор BeanstalkConfig
     */
    public function __construct(ContainerInterface $container)
    {
        try {
            $config = $container->get('config')['beanstalk'];
            $this->connection = Pheanstalk::create(
                $config['host'],
                $config['port'],
                $config['timeout']
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            exit($e->getMessage());
        }
    }

    /**
     * Подключение к хосту
     * @return Pheanstalk
     */
    public function getConnection(): ?Pheanstalk
    {
        return $this->connection;
    }
}