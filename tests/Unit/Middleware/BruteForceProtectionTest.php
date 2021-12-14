<?php

use Idez\NovaSecurity\Http\Middleware\BruteForceProtection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use function Pest\Faker\faker;

beforeAll(function () {
    class TestAuthenticationSessionUser extends User
    {
        protected $table = 'users';
        protected $fillable = ['id', 'name', 'email', 'password', 'blocked_at'];
    }

    Route::middleware(BruteForceProtection::class)->any('/_test/logins', function () {
        return 'OK';
    });
});


beforeEach(function () {
    $this->user = TestAuthenticationSessionUser::fill([
        'name' => faker()->name,
        'email' => faker()->email,
        'password' => bcrypt('secret'),
    ]);
});


it('should be blocked after 3 attempts.', function () {
    config()->set('nova-security.brute_force.max_attempts', 3);

    for ($i = 0; $i < 10; $i++) {
        $this->post('/_test/logins', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        expect($this->user->blocked_at)
            ->toBeTruthy();
    }
});
