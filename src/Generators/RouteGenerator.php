<?php

namespace Anturi\Larastarted\Generators;

use Illuminate\Support\Facades\File;

class RouteGenerator
{
  public function generate(string $name, bool $useMiddleware = false, ?string $middleware = null): void
  {
    $routeFile = base_path("routes/{$name}.php");
    $apiFile = base_path("routes/api.php");
    $routeName = strtolower($name);
    $controllerName = "{$name}Controller::class";

    $routeContent = $this->buildRouteContent($routeName, $controllerName, $useMiddleware, $middleware);
    File::put($routeFile, $routeContent);

    $this->addRequireToApiFile($apiFile, $name);
  }

  private function buildRouteContent(string $routeName, string $controllerName, bool $useMiddleware, ?string $middleware): string
  {
    $content = "<?php\n\nuse Illuminate\Support\Facades\Route;\nuse App\Http\Controllers\\{$controllerName};\n\n";

    if ($useMiddleware && $middleware) {
      $content .= "Route::middleware('{$middleware}')->group(function () {\n";
      $content .= "    Route::apiResource('{$routeName}', {$controllerName});\n";
      $content .= "});\n";
    } else {
      $content .= "Route::apiResource('{$routeName}', {$controllerName});\n";
    }

    return $content;
  }

  private function addRequireToApiFile(string $apiFile, string $name): void
  {
    $requireLine = "require __DIR__ . '/{$name}.php';";
    $apiContent = File::get($apiFile);

    if (!str_contains($apiContent, $requireLine)) {
      File::append($apiFile, "\n" . $requireLine);
    }
  }
}
