<?php

namespace Anturi\Larastarted\Generators;

use Illuminate\Support\Facades\File;

class MigrationGenerator
{
  public function generate(string $tableName, array $fields, array $relations): array
  {
    $migrationFileName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
    $migrationPath = database_path("migrations/{$migrationFileName}");

    $migrationContent = $this->buildMigrationContent($tableName, $fields, $relations);
    File::put($migrationPath, $migrationContent);

    // Agregar campos de relación a los fields para el fillable
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

  private function buildMigrationContent(string $tableName, array $fields, array $relations): string
  {
    $content = "<?php\n\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\n\nreturn new class extends Migration {\n    public function up()\n    {\n        Schema::create('{$tableName}', function (Blueprint \$table) {\n            \$table->id();\n";

    // Campos normales
    foreach ($fields as $field) {
      $content .= $this->buildFieldLine($field);
    }

    // Relaciones
    foreach ($relations as $relation) {
      $content .= "            \$table->unsignedBigInteger('{$relation['relationField']}');\n";
      $content .= "            \$table->foreign('{$relation['relationField']}')->references('{$relation['reference']}')->on('{$relation['referenceTable']}')";
      $content .= $relation['cascade'] ? "->onDelete('cascade')->onUpdate('cascade');\n" : ";\n";
    }

    $content .= "            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::dropIfExists('{$tableName}');\n    }\n};\n";

    return $content;
  }

  private function buildFieldLine(array $field): string
  {
    $line = "            \$table->{$field['fieldType']}('{$field['fieldName']}'";

    if (!empty($field['length']) && is_numeric($field['length'])) {
      $line .= ", {$field['length']}";
    }

    $line .= ")";
    $line .= $field['nullable'] ? "->nullable()" : "";
    $line .= ";\n";

    return $line;
  }
}
