<?php

namespace Idez\NovaSecurity\Middleware;

use Closure;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use RuntimeException;

final class BruteForceProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! ($request->routeIs('nova.login'))) {
            return $next($request);
        }

        $user = $this->getUser($request);
        if (! $user) {
            return $next($request);
        }

        $field = config('nova-security.username_field');

        if (filled($user->getAttribute('blocked_at'))) {
            throw ValidationException::withMessages([
                'email' => [trans('nova-security.blocked')],
            ]);
        }

        $key = "nova-brute-force:{$request?->input($field)}";
        $attempts = Cache::get($key, 0);
        $match = Hash::check($request->input($field), $user?->getAttribute('password'));
        if ($match) {
            Cache::forget($key);

            return $next($request);
        }

        Cache::put($key, ++$attempts, config('nova-security.brute_force.ttl'));

        if ($attempts >= config('nova-security.brute_force.max_attempts')) {
            Cache::forget($key);

            $user->setAttribute('blocked_at', now())->save();

            throw ValidationException::withMessages([
                'email' => [trans('nova-security.brute_force.max_login_attempts')],
            ]);
        }

        return $next($request);
    }

    private function getUser(Request $request): ?AuthUser
    {
        /**
         * @var AuthUser $user
         */
        $model = config('nova-security.user_model');

        if(! $model instanceof AuthUser) {
            throw new RuntimeException('Invalid user model');
        }


        $field = config('nova-security.username_field');
        if(blank($field)) {
            throw new RuntimeException('Invalid username field');
        }


        return $model::where($field, '==', $request->input($field))->first();
    }
}
