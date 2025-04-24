<?php

namespace Anturi\Larastarted\Generators;

use Illuminate\Support\Facades\File;

class ControllerGenerator
{
  public function generate(string $name, string $tableName, array $fields): void
  {
    $controllerPath = app_path("Http/Controllers/{$name}Controller.php");
    $validationRules = $this->buildValidationRules($fields);

    $controllerContent = $this->buildControllerContent($name, $tableName, $validationRules);
    File::put($controllerPath, $controllerContent);
  }

  private function buildValidationRules(array $fields): string
  {
    $rules = [];
    foreach ($fields as $field) {
      $fieldRules = [$field['nullable'] ? 'nullable' : 'required'];

      switch ($field['fieldType']) {
        case 'string':
          $fieldRules[] = 'string';
          if (!empty($field['length'])) {
            $fieldRules[] = "max:{$field['length']}";
          }
          break;
        case 'integer':
        case 'unsignedBigInteger':
          $fieldRules[] = 'integer';
          break;
        case 'boolean':
          $fieldRules[] = 'boolean';
          break;
        case 'text':
          $fieldRules[] = 'string';
          break;
        case 'date':
          $fieldRules[] = 'date';
          break;
      }

      $rules[] = "            '{$field['fieldName']}' => '" . implode('|', $fieldRules) . "'";
    }

    return implode(",\n", $rules);
  }

  private function buildControllerContent(string $name, string $tableName, string $validationRules): string
  {
    return <<<EOT
<?php

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
        parent::__construct(\$model, \$this->class, \$this->responseName, \$this->table);
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
        return \$this->antShow(\$id, \$this->model);
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
EOT;
  }
}
