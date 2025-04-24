<?php

namespace Anturi\Larastarted\Generators;

use Illuminate\Support\Facades\File;

class ModelGenerator
{
  public function generate(string $name, string $tableName, array $fields, array $relations): void
  {
    $modelPath = app_path("Models/{$name}.php");
    $fillableArray = $this->buildFillableArray($fields);
    $relationsContent = $this->buildRelationsContent($relations);

    $modelContent = $this->buildModelContent($name, $tableName, $fillableArray, $relationsContent);
    File::put($modelPath, $modelContent);
  }

  private function buildFillableArray(array $fields): string
  {
    $fillableFields = array_map(fn($field) => "'{$field['fieldName']}'", $fields);
    return implode(', ', $fillableFields);
  }

  private function buildRelationsContent(array $relations): string
  {
    $content = '';
    foreach ($relations as $relation) {
      $methodName = str_replace('_id', '', $relation['relationField']);
      $relatedModel = ucfirst($methodName);
      $content .= "\n    public function {$methodName}()\n    {\n        return \$this->belongsTo({$relatedModel}::class);\n    }\n";
    }
    return $content;
  }

  private function buildModelContent(string $name, string $tableName, string $fillableArray, string $relationsContent): string
  {
    return <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$table = '{$tableName}';
    protected \$fillable = [{$fillableArray}];
{$relationsContent}
}
EOT;
  }
}
