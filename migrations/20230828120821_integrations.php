<?php

require_once 'bootstrap.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

/**
 * Class Integrations
 *
 * @package migrations
 */
class Integrations extends Migration
{
    /**
     * Создать миграцию
     *
     * @return void
     */
    public function up(): void
    {
        Manager::schema()->create('integrations', function ($table) {
            $table->id();
            $table->string('integration_id')->unique();
            $table->string('integration_secret');
            $table->string('redirect_url');
        });
    }

    /**
     * Откатить миграцию
     *
     * @return void
     */
    public function down(): void
    {
        Manager::schema()->dropIfExists('integrations');
    }
}
