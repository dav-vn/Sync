<?php

require_once 'bootstrap.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class UpdateContacts extends Migration
{
    public function up(): void
    {
        Manager::schema()->table('contacts', function ($table) {
            $table->unsignedBigInteger('contact_id');
        });
    }

    /**
     * Откатить изменения
     *
     * @return void
     */
    public function down(): void
    {
        Manager::schema()->table('contacts', function ($table) {
            $table->dropColumn('contact_id');
        });
    }
}
