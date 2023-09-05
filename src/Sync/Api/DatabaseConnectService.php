<?php

namespace Sync\Api;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load('./.env');

/**
 * Class DatabaseConnectService.
 *
 * @package Sync\Api
 */
class DatabaseConnectService
{
    public function __construct()
    {
        $capsule = new Capsule;

        $capsule->addConnection(array(
            'driver' => 'mysql',
            'host' => 'application-mysql',
            'port' => '3306',
            'database' => $_ENV['DB_DATABASE'],
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ));

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}


