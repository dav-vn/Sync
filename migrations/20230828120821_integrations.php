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
     * Do the migration
     */
    public function up()
    {
        Manager::schema()->create('integrations', function ($table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('integration_id')->unique();
            $table->string('integration_secret');
            $table->string('redirect_url');
            $table->timestamps();
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        Manager::schema()->dropIfExists('integrations');
    }
}
