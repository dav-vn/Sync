<?php

declare(strict_types=1);

namespace Sync;

use Sync\Factories\AddIntegrationHandlerFactory;
use Sync\Factories\AuthHandlerFactory;
use Sync\Factories\ContactsHandlerFactory;
use Sync\Factories\GetAccountHandlerFactory;
use Sync\Factories\SumHandlerFactory;
use Sync\Factories\UnisenderContactHandlerFactory;
use Sync\Factories\SendHandlerFactory;
use Sync\Factories\WidgetHandlerFactory;
use Sync\Handlers\AddIntegrationHandler;
use Sync\Handlers\AuthHandler;
use Sync\Handlers\ContactsHandler;
use Sync\Handlers\GetAccountHandler;
use Sync\Handlers\SumHandler;
use Sync\Handlers\UnisenderContactHandler;
use Sync\Handlers\SendHandler;
use Sync\Handlers\WidgetHandler;

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
                SendHandler::class => SendHandlerFactory::class,
                GetAccountHandler::class => GetAccountHandlerFactory::class,
                AddIntegrationHandler::class => AddIntegrationHandlerFactory::class,
                WidgetHandler::class => WidgetHandlerFactory::class,
            ],
        ];
    }
}