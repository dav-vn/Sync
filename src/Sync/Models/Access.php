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
    /** @var bool отключение временных меток */
    public $timestamps = false;

    /** @var array защищенные элементы таблицы */
    protected $guarded = [
        'id',
        'base_domain',
        'access_token',
        'refresh_token',
        'expires',
    ];

    /** @var array разрешенные для Mass Assigment */
    protected $fillable = [
        'amo_id',
        'api_key',
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

