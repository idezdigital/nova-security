<?php

use Idez\NovaSecurity\Middleware\BruteForceProtection;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use function Pest\Faker\faker;


beforeAll(function() {
    class TestAuthenticationSessionUser extends User
    {
        protected $table = 'users';
        protected $fillable = ['id', 'name', 'email', 'password', 'blocked_at'];
    }


    config()->set('nova-security.user_model', TestAuthenticationSessionUser::class);
    config()->set('nova-security.username_field', 'email');

    TestAuthenticationSessionUser::forceCreate([
        'name' => 'Arthur Tavares',
        'email' => 'arthur@idez.com.br',
        'password' => \Illuminate\Support\Facades\Hash::make('secret'),
        'blocked_at' => null,
    ]);

    Route::middleware(BruteForceProtection::class)->any('/_test/logins', function () {
        return 'OK';
    });
});


beforeEach(function() {
    $this->user = TestAuthenticationSessionUser::forceCreate([
        'name' => faker()->name,
        'email' => faker()->email,
        'password' => bcrypt('secret'),
    ]);
});


it('should be blocked after 3 attempts.', function () {

    config()->set('nova-security.brute_force.max_attempts', 3);

    for($i = 0; $i < 10; $i++) {
       $this->post('/_test/logins', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        expect($this->user->blocked_at)
            ->toBeTruthy();
    }
});



