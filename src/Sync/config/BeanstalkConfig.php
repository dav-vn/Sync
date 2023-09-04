<?php

namespace Sync\config;

use Pheanstalk\Pheanstalk;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BeanstalkConfig
{
    private ?Pheanstalk $connection;

    public function __construct(ContainerInterface $container)
    {
        try {
            $this->connection = Pheanstalk::create(
                11300,
                3306,
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection(): ?Pheanstalk
    {
        return $this->connection;
    }
}