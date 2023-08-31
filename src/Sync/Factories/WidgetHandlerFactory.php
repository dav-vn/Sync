<?php

declare(strict_types=1);

namespace Sync\Factories;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sync\Handlers\WidgetHandler;

/**
 * Class SumHandlerFactory
 *
 * @package Sync\Factories\
 */
class WidgetHandlerFactory
{
    /**
     * Возврат нового экземпляра WidgetHandler
     *
     * @param ContainerInterface $container
     * @return RequestHandlerInterface
     */
    public function __invoke(ContainerInterface $container): RequestHandlerInterface
    {
        return new WidgetHandler;
    }
}