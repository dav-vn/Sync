<?php

namespace Sync\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    /** @var array защита от массового присваивания */
    protected $guarded = [
        'id',
        'name',
        'integration_id',
        'integration_secret',
        'redirect_url',
    ];

    /**
     * Связь с таблицей ключей доступа (Один к одному)
     *
     * @return HasMany
     */
    public function accountsIntegrations(): HasMany
    {
        return $this->HasMany(AccountIntegration::class);
    }
}
