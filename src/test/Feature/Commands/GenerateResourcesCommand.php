<?php

namespace Tests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GenerateResourcesCommandTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // Crear archivo api.php si no existe
    if (!file_exists(base_path('routes/api.php'))) {
      File::put(base_path('routes/api.php'), "<?php\n\n");
    }
  }

  protected function tearDown(): void
  {
    // Limpiar archivos generados
    $filesToClean = [
      app_path('Models/Post.php'),
      app_path('Http/Controllers/PostController.php'),
      base_path('routes/Post.php'),
      database_path('migrations/*_create_posts_table.php'),
    ];

    foreach ($filesToClean as $pattern) {
      array_map('unlink', glob($pattern));
    }

    // Limpiar api.php
    File::put(base_path('routes/api.php'), "<?php\n\n");

    parent::tearDown();
  }

  public function test_generates_all_resources()
  {
    $this->artisan('anturi:generate Post posts')
      ->expectsQuestion('¿Do you with to create a migration?', true)
      ->expectsQuestion('Name of field  (let empty to finish)', 'title')
      ->expectsQuestion("Select the data type for 'title'", 'string')
      ->expectsQuestion("Length for 'title'? (leave empty to use default)", '255')
      ->expectsQuestion("Can the 'title' field be nullable?", false)
      ->expectsQuestion("Field name (leave empty to finish)", '')
      ->expectsQuestion("Do you want to add relationships?", false)
      ->expectsQuestion("Do you want to add a middleware to the route?", false)
      ->assertExitCode(0);

    // Verificar archivos generados
    $this->assertFileExists(app_path('Models/Post.php'));
    $this->assertFileExists(app_path('Http/Controllers/PostController.php'));

    $migrationFiles = glob(database_path('migrations/*_create_posts_table.php'));
    $this->assertCount(1, $migrationFiles);

    $this->assertFileExists(base_path('routes/Post.php'));

    // Verificar que se agregó el require a api.php
    $apiContent = File::get(base_path('routes/api.php'));
    $this->assertStringContainsString("require __DIR__ . '/Post.php'", $apiContent);
  }

  public function test_aborts_when_api_route_missing()
  {
    File::delete(base_path('routes/api.php'));

    $this->artisan('anturi:generate Post posts')
      ->expectsOutput('¡Error! El archivo \'routes/api.php\' no existe.')
      ->expectsOutput('Por favor, ejecuta el siguiente comando primero:')
      ->expectsOutput('php artisan install:api')
      ->assertExitCode(1);
  }
}
