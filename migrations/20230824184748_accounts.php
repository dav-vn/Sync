<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager;


class Accounts extends Migration
{
    /**
     * Do the migration
     */
    public function up(): void
    {
        Manager::schema()->create('users', function ($table) {
            $table->id();
            $table->integer('integration_id');
            $table->string('name');
            $table->timestamps();
        });
    }
    /**
     * Undo the migration
     */
    public function down(): void
    {
        Manager::schema()->dropIfExists('accounts');
    }
}
