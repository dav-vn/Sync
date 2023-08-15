<?php

declare(strict_types=1);

namespace Sync\Factories;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\AuthHandler;

/**
 * Class AuthHandlerFactory
 *
 * @package Sync\Factories\
 */
class AuthHandlerFactory
{

    /**
     * Возврат нового экземпляра Auth/Handler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new AuthHandler();
    }

}