<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $creator_id
 * @property string $name
 * @property string $fqn
 * @property Status $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $creator
 * @property-read Collection<int, User> $users
 */
class Repository extends Model
{
    protected $casts = [
        'status' => Status::class,
    ];
    protected static function booted(): void
    {
        static::creating(function (self $repository) {
            $repository->creator_id ??= auth()->id();
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(RepositoryPermission::class, 'repository_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_repositories')
            ->withPivot(['permissions'])
            ->using(RepositoryPermission::class)
            ->as('permissions');
    }
}
