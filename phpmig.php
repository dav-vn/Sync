<?php

use Phpmig\Adapter;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

$container = new Container();

$container['config'] = [
    'host'      => '127.0.0.1',
    'port' => '3306',
    'prefix'    => '',
    'driver' => 'mysql',
    'username' => 'admin',
    'password' => '111111',
    'database' => 'app_db',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
];

$container['db'] = function ($c) {
    $capsule = new Capsule();
    $capsule->addConnection($c['config']);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['phpmig.adapter'] = function($c) {
    return new Adapter\Illuminate\Database($c['db'], 'migrations');
};
$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

return $container;