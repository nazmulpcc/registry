<?php

namespace Illuminate\Database\Eloquent {
    use Illuminate\Support\Carbon;
    /**
     * @property string|int $id
     * @property Carbon $created_at
     * @property Carbon $updated_at
     * @method static static find(int|string $key)
     * @method static static findOrFail(int|string $key)
     * @method static static create(array $attributes = [])
     * @mixin Builder
     */
    class Model
    {}
}

namespace Filament {
    use Illuminate\Auth\RequestGuard;
    class FilamentManager
    {
        public function auth(): RequestGuard{}
    }
}

namespace Illuminate\Auth {
    use App\Models\User;
    class RequestGuard
    {
        public function user(): User {}
    }
}
