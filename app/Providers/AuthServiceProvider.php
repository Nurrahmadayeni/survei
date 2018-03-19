<?php

namespace App\Providers;

use App\UserAuth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('SSU-menu', function ($user)
        {
            return (
            UserAuth::where('username', $user->username)
                ->where('auth_type', 'SSU')
                ->first()
            );
        });
        
        Gate::define('SU-menu', function ($user)
        {
            return (
            UserAuth::where('username', $user->username)
                ->where('auth_type', 'SU')
                ->first()
            );
        });

        Gate::define('admin-menu', function ($user)
        {
            return (
                UserAuth::where('username', $user->username)
                    ->where('auth_type', 'SU')
                    ->exists()
                ||
                UserAuth::where('username', $user->username)
                    ->where('auth_type', 'SSU')
                    ->exists()
                ||
                UserAuth::where('username', $user->username)
                    ->where('auth_type', 'OPU')
                    ->exists()
                ||
                 UserAuth::where('username', $user->username)
                ->where('auth_type', 'OPF')
                ->exists()
            );
        });
    }
}
