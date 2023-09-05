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
    ];

    /** @var array разрешенные для Mass Assigment */
    protected $fillable = [
        'amo_id',
        'api_key',
        'base_domain',
        'id',
        'access_token',
        'refresh_token',
        'expires',
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

