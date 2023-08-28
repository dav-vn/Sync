<?php

namespace Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AccountIntegration
 *
 * @package Sync\Models
 */
class AccountIntegration extends Model
{
    /** @var array защита от массового присваивания */
    protected $guarded = [
        'integration_id',
        'account_id',
    ];

    /** @var string связь с таблицей */
    protected $table = 'accounts_integrations';

    /**
     * Связь с таблицей аккаунтов
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Связь с таблицей интеграций
     *
     * @return BelongsTo
     */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}