<?php

declare(strict_types=1);

namespace Sync;


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
                \Sync\Handlers\SumHandler::class => \Sync\Factories\SumHandlerFactory::class,
                \Sync\Handlers\AuthHandler::class => \Sync\Factories\AuthHandlerFactory::class,
                \Sync\Handlers\ContactsHandler::class => \Sync\Factories\ContactsHandlerFactory::class,
                \Sync\Handlers\UnisenderContactHandler::class => \Sync\Factories\UnisenderContactHandlerFactory::class,
            ],
        ];
    }
}