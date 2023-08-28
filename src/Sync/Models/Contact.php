<?php

namespace Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Contact
 *
 * @package Sync\Models
 */
class Contact extends Model
{
    /** @var array доступны для массового присваивания */
    protected $fillable = [
        'id',
        'account_id',
        'name',
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