<?php

require_once 'bootstrap.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class UpdateContacts extends Migration
{
    public function up(): void
    {
        Manager::schema()->create('contacts', function ($table) {
            $table->id();
            $table->unsignedBigInteger('amo_id');
            $table->string('name');
            $table->string('email');
            $table->unsignedBigInteger('contact_id');

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
        Manager::schema()->dropIfExists('contacts');
    }
}
