<?php

require_once './config/autoload/database.global.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class Accesses extends Migration
{
    /**
     * Создать миграцию
     *
     * @return void
     */
    public function up(): void
    {
        Manager::schema()->create('accesses', function ($table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('base_domain');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('expires');
            $table->text('api_key');

            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Откатить миграцию
     *
     * @return void
     */
    public function down(): void
    {
        Manager::schema()->dropIfExists('accesses');
    }
}
