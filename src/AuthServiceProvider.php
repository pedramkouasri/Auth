<?php

namespace PedApp\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use PedApp\Auth\Events\Register;
use PedApp\Auth\Http\Middleware\Admin;
use PedApp\Auth\Utils\Services\Helper;
use PedApp\Auth\Utils\Services\UserDeviceService;
use Event;

class AuthServiceProvider extends  ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__."/routes/api.php");
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        //register service provider
        $this->app->register(\Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class);
        //call fecad
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('JWTAuth' ,\Tymon\JWTAuth\Facades\JWTAuth::class);
        $loader->alias('JWTFactory' ,\Tymon\JWTAuth\Facades\JWTFactory::class);

        //middleware
        $this->app['router']->middleware('admin' , Admin::class);

        $this->publishes([
            __DIR__.'/database/seeds' => database_path('seeds')
        ], 'seed');

        $this->publishes([
            __DIR__.'/Models' => app_path('Models')
        ], 'model');


        //load views
        $this->loadViewsFrom(__DIR__.'/Views' , 'Auth');

        //for Event
        Event::subscribe(Register::class);

//        $this->loadTranslationsFrom(__DIR__.'/path/to/translations', 'courier');
//        $this->loadViewsFrom(__DIR__.'/path/to/views', 'courier');

//    $this->publishes([
//        __DIR__.'/../config/package.php' => config_path('package.php')
//    ], 'config');
//
//    $this->publishes([
//        __DIR__.'/../database/migrations/' => database_path('migrations')
//    ], 'migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       $this->app->bind('pedapp-auth' , function(){
           return new Auth();
       });

        $this->app->singleton('helper' , function(){
            return new Helper();
        });

        $this->app->singleton('jhoobin' , function(){
            return new Utils\Jhoobin\Jhoobin(config('Auth.access_token'));
        });

        $this->app->singleton('jhoobin_service' , function(){
            return new Utils\Jhoobin\JhoobinService();
        });

        $this->mergeConfigFrom(__DIR__.'/Configs/myConfig.php' ,"Auth" );

    }

}