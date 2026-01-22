<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use App\Events\AttendeSignUpEvent;
use App\Listeners\EventCapacityControlListener;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //Custom blade directive for role-based content. The directive defines on the blade template the returned value. In this case an opening
        //close with an if statement checking the user role.
        Blade::directive('role', function( $role ) {
            return "<?php if(auth()->check() && auth()->user()->role === {$role}): ?> ";
        });

        //endrole is used to close the if and the opening defined in the role directive.
        Blade::directive('endrole', function(){
            return "<?php endif; ?>";
        });
    }
}
