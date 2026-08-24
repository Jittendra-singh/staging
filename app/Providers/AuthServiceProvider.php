<?php
namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\RoleMenuPermission;
use App\Models\User;
use App\Models\Menu;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('create', function (User $user, $module) {
            return $this->isAuthozied($user, array_search( 'Create' , config('app.menu_permissions')) , $module);
        });
        Gate::define('view', function (User $user, $module) {
            return $this->isAuthozied($user, array_search( 'View' , config('app.menu_permissions')) , $module);
        });
        Gate::define('update', function (User $user, $module) {
            return $this->isAuthozied($user, array_search( 'Update' , config('app.menu_permissions')) , $module);
        });
        Gate::define('delete', function (User $user, $module) {
            return $this->isAuthozied($user, array_search( 'Delete' , config('app.menu_permissions')) , $module);
        });
        Gate::define('update-profile', function (User $user, $module) {
            return $this->isAuthozied($user, array_search( 'UpdateProfile' , config('app.menu_permissions')) , $module);
        });
    }
    private function isAuthozied($user , $action , $module){
        $menu   = Menu::where('menu_name' , $module)->first();
        if (!$menu || !is_array($menu->menu_permissions)) {
            return false;
        }

        if(!in_array($action , $menu->menu_permissions) ){
            return false;
        }
        if($user->role_id == 1){
            return true; 
        }
        $result = RoleMenuPermission::where("role_id" , $user->role_id )->where("menu_id" , $menu->id )
                    ->where("permission_id" , $action)->first();
        return $result ? true : false;
    }
}
