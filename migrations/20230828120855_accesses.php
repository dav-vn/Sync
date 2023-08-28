<?php

require_once './config/autoload/database.global.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class Accesses extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        Manager::schema()->create('accesses', function ($table) {
            $table->id();
            $table->foreignId('account_id');
            $table->string('base_domain');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('expires');
            $table->text('api_key');
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        Manager::schema()->dropIfExists('accesses');
    }
}
