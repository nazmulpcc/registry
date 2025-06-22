<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $repository_id
 * @property int $user_id
 * @property array $permissions
 * @property-read array $formated_permissions
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class RepositoryPermission extends Pivot
{
    protected $table = 'repository_user';

    public $timestamps = true;

    protected $primaryKey = 'repository_id';

    protected $casts = [
        'permissions' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class, 'repository_id');
    }

    public function getFormatedPermissionsAttribute(): array
    {
        return array_keys(array_filter($this->permissions));
    }
}
