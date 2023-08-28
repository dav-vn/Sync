<?php

require_once 'bootstrap.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class Contacts extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        Manager::schema()->create('contacts', function ($table) {
            $table->id();
            $table->foreignId('account_id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        Manager::schema()->dropIfExists('contacts');
    }
}
