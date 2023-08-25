<?php

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class AccountsIntegrations extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        Manager::schema()->create('users', function ($table) {
            $table->foreignId('account_id')->constrained();
            $table->foreignId('integration_id')->constrained();
            $table->timestamps();
            $table->primary([
                'integrationId',
                'accountId',
            ]);
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
