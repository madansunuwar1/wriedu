<?php

namespace App\Providers;

use App\Models\User;
use App\Models\lead;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
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

        $permissions = ['view_users', 'create_users', 
        
        'create_leads', 'view_leads','edit_leads','delete_leads', 'convert_leads',
        'view_data_entries','create_data_entries','edit_data_entries', 'update_data_entries', 'delete_data_entries',
        'view_enquiries', 'create_enquiries', 'edit_enquiries', 'update_enquiries', 'delete_enquiries',
    'view_uploads', 'create_uploads', 'edit_uploads', 'update_uploads', 'delete_uploads',
    'view_products', 'create_products', 'edit_products', 'update_products', 'delete_products',
    'view_finances', 'create_finances', 'edit_finances', 'update_finances', 'delete_finances',
    'view_applications', 'create_applications', 'edit_applications', 'update_applications', 'delete_applications',];

foreach ($permissions as $permission) {
    Gate::define($permission, function (User $user) use ($permission) {
        return $user->hasPermission($permission);
    });
    }
    }}
