<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $permissions = Permission::all();

        // Every permission may have multiple roles assigned
        foreach ($permissions as $permission) {
            Gate::define($permission->slug, function ($user) use ($permission) {
                dd($user);
                return $user->hasPermission($permission->slug);
            });
        }
    }
}
