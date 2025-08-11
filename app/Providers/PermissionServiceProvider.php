<?php
namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            // Only load permissions if the permissions table exists
            if (\Schema::hasTable('permissions')) {
                Permission::get()->map(function ($permission) {
                    Gate::define($permission->slug, function ($user) use ($permission) {
                        return $user->hasPermission($permission->slug);
                    });
                });
            }
        } catch (\Exception $e) {
            // Handle exception if the table doesn't exist yet
            // This might happen during fresh installations
        }
    }

    public function register()
    {
        //
    }
}