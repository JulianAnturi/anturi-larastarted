<?php

namespace anturi\larastarted\Providers;

use Illuminate\Support\ServiceProvider;
use anturi\larastarted\Commands\GenerateResourcesCommand;
use Illuminate\Support\Facades\App;
class MiPaqueteServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar el helper de respuestas
        $this->app->singleton('ResponseService', function ($app) {
            return new \anturi\larastarted\Helpers\ResponseService();
        });
    }

    public function boot()
    {
        // Cargar rutas personalizadas
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');

        // Publicar archivos de configuración y controladores
        $this->publishes([
            __DIR__.'/../../config/mi_paquete.php' => App::config_path('mi_paquete.php'),
            __DIR__.'/../../src/Controllers/BaseController.php' => App::app_path('Http/Controllers/BaseController.php'),
        ], 'mi-paquete');

        // Registrar el comando artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateResourcesCommand::class,
            ]);
        }
    }
}

