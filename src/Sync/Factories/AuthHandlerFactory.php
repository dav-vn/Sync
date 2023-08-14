<?php

declare(strict_types=1);

namespace Sync\Factories;

use Sync\Handlers\AuthHandler;
use Sync\Handlers\SumHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SumHandlerFactory {

    public function __invoke(ContainerInterface $container): RequestHandlerInterface {
        return new AuthHandler;
    }

}