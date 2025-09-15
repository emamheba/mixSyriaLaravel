<?php

namespace App\Providers;

use App\Facades\ModuleDataFacade;
use App\Helpers\ModuleMetaData;
use App\Models\Backend\Language;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('ModuleDataFacade',function(){
            return new ModuleMetaData();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
              return [
                'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' :
                          (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
              ];
            }
            return [];
          });

        Schema::defaultStringLength(191);
        try {
            $all_language = Language::all();
        }catch (\Exception $e){
            $all_language = null;
        }

        Paginator::useBootstrap();
        if (get_static_option('site_force_ssl_redirection') === 'on'){
            URL::forceScheme('https');
        }
        Paginator::useBootstrap();
        $this->loadViewsFrom(__DIR__.'/../../plugins/PageBuilder/views','pagebuilder');
    }
}
