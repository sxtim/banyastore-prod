<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('perm', function ($perm) {
            return "<?php if(auth()->check() && auth()->user()->hasPermAnyWay({$perm})): ?>";
        });
        Blade::directive('endperm', function ($perm) {
            return "<?php endif; ?>";
        });
        Blade::directive('allperms', function ($perms) {
            return "<?php if(auth()->check() && auth()->user()->hasAllPerms({$perms})): ?>";
        });
        Blade::directive('endallperms', function ($perms) {
            return "<?php endif; ?>";
        });
        Blade::directive('anyperms', function ($perms) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyPerms({$perms})): ?>";
        });
        Blade::directive('endanyperms', function ($perms) {
            return "<?php endif; ?>";
        });
    }
}
