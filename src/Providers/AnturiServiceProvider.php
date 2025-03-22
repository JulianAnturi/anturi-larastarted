<?php
namespace Anturi\Larastarted\Providers;

use Illuminate\Support\ServiceProvider;
use Anturi\Larastarted\Commands\GenerateResourcesCommand;
use Anturi\Larastarted\Helpers\ResponseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class AnturiServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar el helper de respuestas
        $this->app->singleton('ResponseService', function ($app) {
            return new ResponseService();
        });
    }
    
    public function boot()
    {
        // Cargar rutas personalizadas
        // $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        
        // Publicar archivos de configuración y controladores
      $this->publishes([
        __DIR__.'/../config/larastarted.php' => config_path('larastarted.php'),
        __DIR__.'/../Controllers/BaseController.php' => app_path('Http/Controllers/BaseController.php'),
      ], 'larastarted');

      // Registrar el comando artisan
      if ($this->app->runningInConsole()) {
        Log::info('AnturiServiceProvider boot method called');
        $this->commands([
          GenerateResourcesCommand::class,
        ]);
        Log::info('Commands registered');
      }
    }
}
