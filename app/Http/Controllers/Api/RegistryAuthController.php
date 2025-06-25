<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistryAuthController extends Controller
{
    public function __construct(protected AuthTokenService $service)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        [$email, $accessToken] = [
            $request->headers->get('php-auth-user'),
            $request->headers->get('php-auth-pw'),
        ];

        /** @var User $user */
        $user = User::query()
            ->where('email', $email)
            ->orWhere('username', $email)
            ->first();

        abort_if(! $user || ! $user->validateAccessToken($accessToken), 401, 'Invalid credentials');

        // TODO: validate token scopes with $request->input('scope')

        return response()->json([
            'token' => $this->service->createToken($user, $request->input('scope')),
            'expires_in' => 60 * config('jwt.ttl'),
            'issued_at' => now()->toIso8601String(),
        ]);
    }
}
