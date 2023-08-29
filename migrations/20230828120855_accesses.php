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
            $table->unsignedBigInteger('amo_id');
            $table->string('base_domain');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('expires');
            $table->text('api_key')->nullable();
            $table->timestamps();

            $table->foreign('amo_id')->references('amo_id')->on('accounts');
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
