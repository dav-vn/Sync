<?php

declare(strict_types=1);

namespace Sync\Factories;

use AmoCRM\EntitiesServices\Contacts;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\AuthHandler;
use Sync\Handlers\ContactsHandler;
use Sync\Handlers\UnisenderContactHandler;

/**
 * Class UnisenderContactHandlerFactory
 *
 * @package Sync\Factories\
 */
class UnisenderContactHandlerFactory
{
    /**
     * Возврат нового экземпляра ContactHandler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new UnisenderContactHandler();
    }
}