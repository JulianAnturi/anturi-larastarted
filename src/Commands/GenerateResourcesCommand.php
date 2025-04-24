<?php

namespace Anturi\Larastarted\Commands;

use Illuminate\Console\Command;
use Anturi\Larastarted\Generators\ControllerGenerator;
use Anturi\Larastarted\Generators\MigrationGenerator;
use Anturi\Larastarted\Generators\ModelGenerator;
use Anturi\Larastarted\Generators\RouteGenerator;
use Anturi\Larastarted\Traits\FieldBuilderTrait;

class GenerateResourcesCommand extends Command
{
  use FieldBuilderTrait;

  protected $signature = 'anturi:generate {name} {table}';
  protected $description = 'Genera un Controlador, Modelo y Migración.';

  private MigrationGenerator $migrationGenerator;
  private ModelGenerator $modelGenerator;
  private ControllerGenerator $controllerGenerator;
  private RouteGenerator $routeGenerator;

  public function __construct(
    MigrationGenerator $migrationGenerator,
    ModelGenerator $modelGenerator,
    ControllerGenerator $controllerGenerator,
    RouteGenerator $routeGenerator
  ) {
    parent::__construct();
    $this->migrationGenerator = $migrationGenerator;
    $this->modelGenerator = $modelGenerator;
    $this->controllerGenerator = $controllerGenerator;
    $this->routeGenerator = $routeGenerator;
  }

  public function handle()
  {
    $name = ucfirst($this->argument('name'));
    $tableName = $this->argument('table');

    if (!file_exists(base_path("routes/api.php"))) {
      $this->error("¡Error! El archivo 'routes/api.php' no existe.");
      $this->line("Por favor, ejecuta el siguiente comando primero:");
      $this->info("php artisan install:api");
      return 1;
    }

    // Obtener campos y relaciones interactivamente
    $fields = $this->askForFields();
    $relations = $this->askForRelations();

    // Generar recursos
    $migrationData = $this->migrationGenerator->generate($tableName, $fields, $relations);

    $this->modelGenerator->generate(
      $name,
      $tableName,
      $migrationData['fields'],
      $migrationData['relations']
    );

    $this->controllerGenerator->generate(
      $name,
      $tableName,
      $migrationData['fields']
    );

    $useMiddleware = $this->confirm("¿Deseas agregar un middleware a la ruta?", false);
    $middleware = $useMiddleware ? $this->ask("Nombre del middleware (ej: auth:sanctum)") : null;

    $this->routeGenerator->generate($name, $useMiddleware, $middleware);

    $this->info("Controlador, Modelo y rutas generados para {$name} correctamente.");
  }
}
