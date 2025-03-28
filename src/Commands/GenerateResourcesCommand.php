<?php

namespace Anturi\Larastarted\Commands;
//TODO: los campos de relaciones no se estan llenando en el fillable del archivo migracion 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class GenerateResourcesCommand extends Command
{
  protected $signature = 'anturi:generate {name} {table}';
  protected $description = 'Genera un Controlador, Modelo y Migración.';

  public function handle()
  {
    $name = ucfirst($this->argument('name')); // Capitaliza la primera letra
    $tableName = $this->argument('table'); // Convención: pluraliza el nombre del modelo

    //crea la migracion y retorna los campos listos para entregar en el modelo 
    $migrationData = $this->generateMigrationContent($tableName);
    $fields = $migrationData['fields'];
    $relations = $migrationData['relations'];
    // ✅ Crear Modelo con `$fillable`
    $fillableFields = array_map(fn($field) => "'{$field['fieldName']}'", $fields);
    $fillableArray = implode(", ", $fillableFields);
    $this->generateModelContent($name, $tableName, $fillableArray, $relations);
    // ✅ Crear Controlador
    $this->generateControllerContent($name, $tableName, $fields);
    $this->generateRouteFile($name);

    $this->info("Controlador, Modelo y rutas generados para {$name} correctamente.");
  }

  private function generateControllerContent($name, $tableName, $fields): void
  {
    $controllerPath = App::path("Http/Controllers/{$name}Controller.php");

    $validationRules = "";
    foreach ($fields as $field) {
      $rules = [];
      $rules[] = $field['nullable'] ? 'nullable' : 'required';

      switch ($field['fieldType']) {
      case 'string':
        $rules[] = 'string';
        if (!empty($field['length'])) {
          $rules[] = "max:{$field['length']}";
        }
        break;
      case 'integer':
      case 'unsignedBigInteger':
        $rules[] = 'integer';
        break;
      case 'boolean':
        $rules[] = 'boolean';
        break;
      case 'text':
        $rules[] = 'string';
        break;
      case 'date':
        $rules[] = 'date';
        break;
      }

      $ruleString = implode('|', $rules);
      $validationRules .= "            '{$field['fieldName']}' => '{$ruleString}',\n";
    }

    // Contenido del controlador con validaciones
    $controllerContent = "<?php

namespace App\Http\Controllers;

use App\Models\\{$name} as Model;
use Illuminate\Http\Request;
use Anturi\\Larastarted\\Controllers\\BaseController;
use Anturi\\Larastarted\\Helpers\\ResponseService;

class {$name}Controller extends BaseController
{
  protected \$model;
  protected \$table = '{$tableName}';
  protected \$class = '{$name}Controller';
  protected \$responseName = '{$name}';

  public function __construct(Model \$model)
  {
    parent::__construct(\$model, \$this->class,\$this->responseName,\$this->table);
  }

  public function index(Request \$request)
  {
    return \$this->antIndex(\$request);
  }

  public function store(Request \$request)
  {
    \$this->validateForm(\$request);
    \$data = \$request->all();
    return \$this->antStore(\$data);
  }

  public function show(\$id)
  {
    return \$this->antShow(\$id,\$this->model);
  }

  public function update(Request \$request, \$id)
  {
    \$this->validateForm(\$request);
    \$data = \$request->all();
    return \$this->antUpdate(\$data, \$id);
  }

  public function destroy(\$id)
  {
    return \$this->antDestroy(\$id);
  }

  private function validateForm(Request \$request)
  {
    \$request->validate([
  {$validationRules}        
    ]);
  }
  }
";

    File::put($controllerPath, $controllerContent);
  }

  private function generateModelContent($name, $tableName, $fillableArray, $relations): void
  {

    $modelPath = App::path("Models/{$name}.php");
    $relationsContent = "";
    foreach ($relations as $relation) {
      $methodName = str_replace('_id', '', $relation['relationField']);
      $relatedModel = ucfirst($methodName);
      $relationsContent .= "\n    public function {$methodName}()\n    {\n        return \$this->belongsTo({$relatedModel}::class);\n    }\n";
    }
    $modelContent = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$name} extends Model\n{\n    protected \$table = '{$tableName}';\n    protected \$fillable = [{$fillableArray}];\n\n    {$relationsContent}\n}";
    File::put($modelPath, $modelContent);
  }

  private function generateMigrationContent($tableName): array
  {

    $fields = [];
    $migrationFileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
    $migrationPath = database_path("migrations/{$migrationFileName}");

    if ($this->confirm("¿Deseas crear una migración para {$tableName}?", true)) {
      do {
        $fieldName = $this->ask("Nombre del campo (deja vacío para finalizar)");
        if ($fieldName) {
          $fieldType = $this->choice(
            "Selecciona el tipo de dato para '{$fieldName}'",
            ['string', 'integer', 'unsignedBigInteger', 'boolean', 'text', 'date', 'timestamps'],
            0
          );
          $length = null;
          $typesWithLength = ['string', 'integer'];
          if (in_array($fieldType, $typesWithLength)) {
            $length = $this->ask("¿Longitud para '{$fieldName}'? (deja vacío para usar la predeterminada)");
            $length = is_numeric($length) ? (int) $length : null;
          }
          $isNullable = $this->confirm("¿El campo '{$fieldName}' puede ser nulo?", false);
          $fields[] = [
            'fieldName' => $fieldName,
            'fieldType' => $fieldType,
            'nullable' => $isNullable,
            'length' => $length
          ];
        }
      } while ($fieldName);

      // ✅ Preguntar por relaciones
      $relations = [];
      $relationships = [];
      if ($this->confirm("¿Deseas agregar relaciones?", false)) {
        do {
          $relationField = $this->ask("Nombre del campo de la relación (ejemplo: user_id)");
          if ($relationField) {
            $reference = $this->ask("Campo referenciado (deja vacío para 'id')") ?: 'id';
            $referenceTable = $this->ask("Tabla referenciada (ejemplo: users)");
            $cascade = $this->confirm("¿Cascade ON DELETE y ON UPDATE?", true);
            $relations[] = compact('relationField', 'reference', 'referenceTable', 'cascade');
            $relationships[] = $relationField;
          }
        } while ($this->confirm("¿Deseas agregar otra relación?", false));
      }

      // ✅ Generar contenido de la migración
      $migrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration {\n    public function up()\n    {\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n";
      foreach ($fields as $field) {
        $line = "            \$table->{$field['fieldType']}('{$field['fieldName']}'";
        if (!empty($field['length']) && is_numeric($field['length'])) {
          $line .= ", {$field['length']}";
        }
        $line .= ")";
        if (!empty($field['nullable']) && $field['nullable']) {
          $line .= "->nullable()";
        }
        $line .= ";\n";
        $migrationContent .= $line;
      }

      foreach ($relations as $relation) {
        $migrationContent .= "            \$table->unsignedBigInteger('{$relation['relationField']}');\n";
        $migrationContent .= "            \$table->foreign('{$relation['relationField']}')->references('{$relation['reference']}')->on('{$relation['referenceTable']}')";

        if ($relation['cascade']) {
          $migrationContent .= "->onDelete('cascade')->onUpdate('cascade')";
        }
        $migrationContent .= ";\n";
      }

      $migrationContent .= "            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('{$tableName}');\n    }\n};\n";

      // ✅ Guardar la migración
      File::put($migrationPath, $migrationContent);
      foreach ($relations as $relation) {
        $fields[] = [
          'fieldName' => $relation['relationField'],
          'fieldType' => 'unsignedBigInteger',
          'nullable' => false,
          'length' => null
        ];
      }

      return [
        'fields' => $fields,
        'relations' => $relations
      ];
    }
    return [
      'fields' => [],
      'relations' => []
    ];
  }

  private function generateRouteFile($name): void
  {
    $routeFile = base_path("routes/{$name}.php");
    $apiFile = base_path("routes/api.php");
    $controllerName = "{$name}Controller::class";
    $routeName = strtolower($name); // para usar en la ruta

    // Preguntar por middleware
    $useMiddleware = $this->confirm("¿Deseas agregar un middleware a la ruta?", false);
    $middleware = $useMiddleware ? $this->ask("Nombre del middleware (ej: auth:sanctum)") : null;

    // Generar contenido del archivo de rutas individuales
    $routeContent = "<?php\n\nuse Illuminate\Support\Facades\Route;\nuse App\Http\Controllers\\{$name}Controller;\n\n";
    if ($middleware) {
      $routeContent .= "Route::middleware('{$middleware}')->group(function () {\n";
      $routeContent .= "    Route::apiResource('{$routeName}', {$controllerName});\n";
      $routeContent .= "});\n";
    } else {
      $routeContent .= "Route::apiResource('{$routeName}', {$controllerName});\n";
    }

    // Crear archivo de ruta individual
    File::put($routeFile, $routeContent);
    $this->info("Archivo de rutas creado: routes/{$name}.php");

    // Agregar el require en api.php si no existe aún
    $requireLine = "require __DIR__ . '/{$name}.php';";
    $apiContent = File::get($apiFile);

    if (!str_contains($apiContent, $requireLine)) {
      File::append($apiFile, "\n" . $requireLine);
      $this->info("Línea de require añadida a api.php");
    } else {
      $this->info("El require ya existe en api.php");
    }
  }

}
