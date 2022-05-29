<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\User;


use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // permission for super admin and the hr
        Gate::define('create-delete-users', function(User $user){
            if($user->role_id===1 || $user->role_id === 3){
                return true;
            }
        });

        //permission for an employee only a employee can attend
        Gate::define('create', function(User $user){
            if($user->role_id===2 || $user->role_id === 3){
                return true;
            }
        });

        $frontEndUrl = env('FRONTEND_URL');
        $this->setFrontEndUrlInResetPasswordEmail($frontEndUrl);
        
        //
    }

    protected function setFrontEndUrlInResetPasswordEmail($frontEndUrl = '')
    {
        // update url in ResetPassword Email to frontend url
        ResetPassword::createUrlUsing(function ($user, string $token) use ($frontEndUrl) {
            return $frontEndUrl . '/auth/password/email/reset?token=' . $token;
        });
    }
}
