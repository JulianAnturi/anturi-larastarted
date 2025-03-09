<?php

namespace JulianAnturi\laravelStarted\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class GenerateResourcesCommand extends Command
{
    protected $signature = 'anturi:generate {name}';
    protected $description = 'Genera un Controlador, Modelo y Migración.';

    public function handle()
    {
        $name = ucfirst($this->argument('name')); // Capitaliza la primera letra
        $tableName = $this->argument('table'); // Convención: pluraliza el nombre del modelo

        $controllerPath = App::path("Http/Controllers/{$name}Controller.php");
        $modelPath = App::path("Models/{$name}.php");
        $migrationFileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $migrationPath = App::database_path("migrations/{$migrationFileName}");

        // ✅ Crear Controlador
        File::put($controllerPath, "<?php\n\nnamespace App\Http\Controllers;\n\nuse App\Models\\{$name};\nuse Illuminate\Http\Request;\nuse anturi\laraStarted\Controllers\BaseController;\nuse anturi\laraStarted\Helpers\ResponseService;\nuse anturi\laraStarted\Helpers\CrudService;\n\nclass {$name}Controller extends BaseController\n{\n    protected \$model = {$name}::class;\n    protected \$table = '{$tableName}';\n    protected \$class = '{$name}Controller';\n    protected \$responseName = '{$name}';\n\n    public function __construct(CrudService \$crudService, ResponseService \$responseService)\n    {\n        parent::__construct(\$crudService, \$responseService);\n    }\n\n    /**\n     * Mostrar todos los registros.\n     */\n    public function index(Request \$request)\n    {\n        return \$this->antIndex(\$request);\n    }\n\n    /**\n     * Guardar un nuevo registro en la base de datos.\n     */\n    public function store(Request \$request)\n    {\n        return \$this->antStore(\$request);\n    }\n\n    /**\n     * Mostrar un registro específico.\n     */\n    public function show(\$id)\n    {\n        return \$this->antShow(\$this->table, 'id');\n    }\n\n    /**\n     * Actualizar un registro en la base de datos.\n     */\n    public function update(Request \$request, \$id)\n    {\n        return \$this->antUpdate(\$request, \$id);\n    }\n\n    /**\n     * Eliminar un registro de la base de datos.\n     */\n    public function destroy(\$id)\n    {\n        return \$this->antDestroy(\$id);\n    }\n\n    /**\n     * Seleccionar registros con campos específicos.\n     */\n   // public function select(\$id, \$field = 'name')\n //   {\n       // return \$this->antSelect(\$this->table, \$id, \$field);\n  //  }\n\n    /**\n     * Obtener registros relacionados de otra tabla.\n     */\n  //  public function subSelect(\$table, \$tableId, \$parentTable, \$parentTableId, \$parentIdValue, \$field)\n    {\n        return \$this->antsubSelect(\$table, \$tableId, \$parentTable, \$parentTableId, \$parentIdValue, \$field);\n    }\n}\n");
        // ✅ Preguntar si se debe crear una migración
        $fields = [];
        if ($this->confirm("¿Deseas crear una migración para {$tableName}?", true)) {
          do {
            $fieldName = $this->ask("Nombre del campo (deja vacío para finalizar)");
            if ($fieldName) {
              $fieldType = $this->choice(
                "Selecciona el tipo de dato para '{$fieldName}'",
                ['string', 'integer', 'unsignedBigInteger', 'boolean', 'text', 'date', 'timestamps'],
                0
              );
              $fields[] = compact('fieldName', 'fieldType');
            }
          } while ($fieldName);

          // ✅ Preguntar por relaciones
          $relations = [];
          if ($this->confirm("¿Deseas agregar relaciones?", false)) {
            do {
              $relationField = $this->ask("Nombre del campo de la relación (ejemplo: user_id)");
              if ($relationField) {
                $reference = $this->ask("Campo referenciado (deja vacío para 'id')") ?: 'id';
                $referenceTable = $this->ask("Tabla referenciada (ejemplo: users)");
                $cascade = $this->confirm("¿Cascade ON DELETE y ON UPDATE?", true);
                $relations[] = compact('relationField', 'reference', 'referenceTable', 'cascade');
              }
            } while ($this->confirm("¿Deseas agregar otra relación?", false));
          }

          // ✅ Generar contenido de la migración
          $migrationContent = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration {\n    public function up()\n    {\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n";

          foreach ($fields as $field) {
            $migrationContent .= "            \$table->{$field['fieldType']}('{$field['fieldName']}');\n";
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
          $this->info("Migración creada: {$migrationPath}");
        }

        // ✅ Crear Modelo con `$fillable`
        $fillableFields = array_map(fn ($field) => "'{$field['fieldName']}'", $fields);
        $fillableArray = implode(", ", $fillableFields);

        $modelContent = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$name} extends Model\n{\n    protected \$table = '{$tableName}';\n    protected \$fillable = [{$fillableArray}];\n}\n";

        File::put($modelPath, $modelContent);

        $this->info("Controlador, Modelo y rutas generados para {$name} correctamente.");
    }
}
