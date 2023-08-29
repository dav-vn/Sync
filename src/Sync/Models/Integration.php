<?php

namespace Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    /** @var bool отключение временных меток */
    public $timestamps = false;

    /** @var array доступные для массового присваивания */
    protected $fillable = [
        'id',
        'name',
        'integration_id',
        'integration_secret',
        'redirect_url',
    ];

    /**
     * Связь с таблицей ключей доступа (Один к одному)
     *
     * @return BelongsToMany
     */
    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class);
    }
}
