<?php

declare(strict_types=1);

namespace Sync\Factories;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\SendHandler;

/**
 * Class SendHandlerFactory
 *
 * @package Sync\Factories\
 */
class SendHandlerFactory
{
    /**
     * Возврат нового экземпляра SendHandler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new SendHandler();
    }
}