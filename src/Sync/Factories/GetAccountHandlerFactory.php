<?php

declare(strict_types=1);

namespace Sync\Factories;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\GetAccountHandler;

/**
 * Class GetAccountHandlerFactory
 *
 * @package Sync\Factories\
 */
class GetAccountHandlerFactory
{
    /**
     * Возврат нового экземпляра GetAccountHandler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new GetAccountHandler();
    }
}