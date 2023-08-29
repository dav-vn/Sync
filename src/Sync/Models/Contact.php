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
    /** @var bool отключение временных меток */
    public $timestamps = false;

    /** @var array доступны для массового присваивания */
    protected $fillable = [
        'id',
        'name',
        'email',
        'amo_id',
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