<?php

declare(strict_types=1);

namespace Sync;


use Sync\Factories\AuthHandlerFactory;
use Sync\Factories\ContactsHandlerFactory;
use Sync\Factories\SumHandlerFactory;
use Sync\Factories\UnisenderContactHandlerFactory;
use Sync\Handlers\AuthHandler;
use Sync\Handlers\ContactsHandler;
use Sync\Handlers\SumHandler;
use Sync\Handlers\UnisenderContactHandler;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories' => [
                SumHandler::class => SumHandlerFactory::class,
                AuthHandler::class => AuthHandlerFactory::class,
                ContactsHandler::class => ContactsHandlerFactory::class,
                UnisenderContactHandler::class => UnisenderContactHandlerFactory::class,
            ],
        ];
    }
}