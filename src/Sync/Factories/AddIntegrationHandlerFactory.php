<?php

declare(strict_types=1);

namespace Sync\Factories;

use Sync\Handlers\AddIntegrationHandler;
use Sync\Handlers\SumHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class SumHandlerFactory
 *
 * @package Sync\Factories\
 */
class AddIntegrationHandlerFactory
{
    /**
     * Возврат нового экземпляра SumHandler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new AddIntegrationHandler();
    }
}