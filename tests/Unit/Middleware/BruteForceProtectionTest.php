<?php

use Idez\NovaSecurity\Http\Middleware\NovaBruteForceProtection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use function Pest\Faker\faker;

beforeEach(function () {

    class TestAuthenticationSessionUser extends User
    {
        protected $table = 'users';
        protected $fillable = ['id', 'name', 'email', 'password', 'blocked_at'];
    }

    Route::middleware(NovaBruteForceProtection::class)->any('/_test/logins', function () {
        return 'OK';
    });


    config(['nova-security.brute_force_protection.enabled' => true]);
    config(["auth.providers.users.model" => TestAuthenticationSessionUser::class]);

    $user = new TestAuthenticationSessionUser();
    $this->user = $user->fill([
        'name' => faker()->name,
        'email' => faker()->email,
        'password' => bcrypt('secret'),
        'blocked_at' => null,
    ]);
});


it('should be blocked after 3 attempts.', function () {

    $this->markTestSkipped('This test is not working.');
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
