<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule = new Capsule;

$capsule->addConnection(array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'app_db',
    'username' => 'admin',
    'password' => '111111',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
));

$capsule->setAsGlobal();
$capsule->bootEloquent();