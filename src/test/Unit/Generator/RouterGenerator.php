<?php

namespace Tests\Unit\Generators;

use Anturi\Larastarted\Generators\RouteGenerator;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class RouteGeneratorTest extends TestCase
{
  private RouteGenerator $generator;

  protected function setUp(): void
  {
    parent::setUp();
    $this->generator = new RouteGenerator();

    // Crear archivo api.php de prueba
    if (!file_exists(base_path('routes/api.php'))) {
      file_put_contents(base_path('routes/api.php'), "<?php\n\n");
    }
  }

  protected function tearDown(): void
  {
    if (file_exists(base_path('routes/Test.php'))) {
      unlink(base_path('routes/Test.php'));
    }

    // Limpiar api.php
    file_put_contents(base_path('routes/api.php'), "<?php\n\n");
    parent::tearDown();
  }

  public function test_generate_creates_route_file()
  {
    $name = 'Test';

    $this->generator->generate($name);

    $this->assertFileExists(base_path("routes/{$name}.php"));

    $content = file_get_contents(base_path("routes/{$name}.php"));
    $this->assertStringContainsString("Route::apiResource('test', TestController::class)", $content);
  }

  public function test_generate_with_middleware()
  {
    $name = 'Protected';
    $middleware = 'auth:sanctum';

    $this->generator->generate($name, true, $middleware);

    $content = file_get_contents(base_path("routes/{$name}.php"));
    $this->assertStringContainsString("Route::middleware('{$middleware}')", $content);
    $this->assertStringContainsString("Route::apiResource('protected', ProtectedController::class)", $content);
  }

  public function test_add_require_to_api_file()
  {
    $name = 'Test';

    $this->generator->generate($name);

    $apiContent = file_get_contents(base_path('routes/api.php'));
    $this->assertStringContainsString("require __DIR__ . '/{$name}.php'", $apiContent);
  }
}
