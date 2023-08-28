<?php

namespace Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


/**
 * Class Account
 *
 * @package Sync\Models
 */
class Account extends Model
{
    /** @var array защищенные элементы таблицы */
    protected $guarded = [
        'id',
        'name',
    ];

    /**
     * Связь с таблицей контактов
     *
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Связь с таблицей связывающей аккаунты и интеграции (многие ко многим)
     *
     * @return HasMany
     */
    public function accountsIntegrations(): HasMany
    {
        return $this->hasMany(AccountIntegration::class);
    }

    /**
     * Связь с таблицей ключей доступа (Один к одному)
     *
     * @return HasOne
     */
    public function accesses(): HasOne
    {
        return $this->hasOne(Access::class);
    }
}