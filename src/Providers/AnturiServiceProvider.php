<?php

namespace Anturi\Larastarted\Providers;

use Illuminate\Support\ServiceProvider;
use Anturi\Larastarted\Commands\GenerateResourcesCommand;
use Anturi\Larastarted\Helpers\ResponseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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
    // Publicar archivos con modificación del namespace para BaseController
    $this->publishes([
      __DIR__ . '/../config/larastarted.php' => config_path('larastarted.php'),
      __DIR__ . '/../Migrations/2024_03_09_create_logs_table.php' => database_path('migrations/2024_03_09_create_logs_table.php'),
    ], 'anturi-larastarted');

    // Publicar BaseController con namespace modificado
    $this->publishBaseControllerWithCustomNamespace();

    // Registrar el comando artisan
    if ($this->app->runningInConsole()) {
      Log::info('AnturiServiceProvider boot method called');
      $this->commands([
        GenerateResourcesCommand::class,
      ]);
      $this->loadMigrationsFrom(__DIR__ . '/../Migrations');
      Log::info('Commands registered');
    }
  }

  protected function publishBaseControllerWithCustomNamespace()
  {
    $source = __DIR__ . '/../Controllers/BaseController.php';
    $destination = app_path('Http/Controllers/BaseController.php');

    // Solo publicar si no existe o está en modo force
    if (
      $this->app->runningInConsole() &&
      ($this->isPublishCommand() || !File::exists($destination))
    ) {

      $content = File::get($source);
      $modifiedContent = str_replace(
        'namespace Anturi\Larastarted\Controllers;',
        'namespace App\Http\Controllers;',
        $content
      );

      // Asegurar que el directorio existe
      File::ensureDirectoryExists(dirname($destination));

      // Escribir el archivo modificado
      File::put($destination, $modifiedContent);
    }
  }

  protected function isPublishCommand(): bool
  {
    return str_contains(request()->server('argv')[1] ?? '', 'vendor:publish');
  }
}
