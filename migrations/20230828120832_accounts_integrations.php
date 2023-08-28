<?php

require_once 'bootstrap.php';

use Phpmig\Migration\Migration;
use Illuminate\Database\Capsule\Manager;

class AccountsIntegrations extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        Manager::schema()->create('accounts_integrations', function ($table) {
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('integration_id')->constrained('integrations');
            $table->timestamps();
            $table->primary([
                'integration_id',
                'account_id',
            ]);
        });
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        Manager::schema()->dropIfExists('accounts_integrations');
    }
}
