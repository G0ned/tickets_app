<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Event;
use App\Observers\EventObserver;

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
        //Registro del observer de Eventos.
        Event::observe(EventObserver::class);

        Blade::directive('admin', function() {
            return "<?php if(auth()->check() && auth()->user()->is_admin): ?> ";
        });
        
        Blade::directive('endadmin', function(){
            return "<?php endif; ?>";
        });

        Blade::directive('super', function(){
            return "<?php if(auth()->check() && auth()->user()->is_supervisor): ?>";
        });

        Blade::directive('endsuper', function(){
            return "<?php endif; ?>";
        });
    }
}
