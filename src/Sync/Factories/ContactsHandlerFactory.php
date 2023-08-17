<?php

declare(strict_types=1);

namespace Sync\Factories;

use AmoCRM\EntitiesServices\Contacts;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\AuthHandler;
use Sync\Handlers\ContactsHandler;

/**
 * Class ContactHandlerFactory
 *
 * @package Sync\Factories\
 */
class ContactsHandlerFactory
{
    /**
     * Возврат нового экземпляра ContactHandler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new ContactsHandler();
    }
}