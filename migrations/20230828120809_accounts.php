<?php

require_once 'bootstrap.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

/**
 * Class Accounts
 *
 * @package migrations
 */
class Accounts extends Migration
{
    /**
     * Do the migration
     */
    public function up(): void
    {
        Manager::schema()->create('accounts', function ($table) {
            $table->id();
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
