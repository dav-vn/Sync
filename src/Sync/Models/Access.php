<?php

namespace Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Access
 *
 * @package Sync\Models
 */
class Access extends Model
{
    /** @var array защищенные элементы таблицы */
    protected $guarded = [
        'id',
        'account_id',
        'base_domain',
        'access_token',
        'refresh_token',
        'expires',
        'api_key',
        'created_at',
        'updated_at',
    ];

    /**
     * Связь с таблицей аккаунтов
     *
     * @return BelongsTo
     */
    public function accounts(): BelongsTo
    {
        return $this->belongsTo(Access::class);
    }
}

