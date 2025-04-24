<?php

namespace Tests\Unit\Generators;

use Anturi\Larastarted\Generators\ControllerGenerator;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ControllerGeneratorTest extends TestCase
{
  private ControllerGenerator $generator;

  protected function setUp(): void
  {
    parent::setUp();
    $this->generator = new ControllerGenerator();
  }

  protected function tearDown(): void
  {
    if (file_exists(app_path('Http/Controllers/TestController.php'))) {
      unlink(app_path('Http/Controllers/TestController.php'));
    }
    parent::tearDown();
  }

  public function test_generate_creates_controller_file()
  {
    $name = 'Test';
    $tableName = 'tests';
    $fields = [
      ['fieldName' => 'name', 'fieldType' => 'string', 'nullable' => false, 'length' => 255]
    ];

    $this->generator->generate($name, $tableName, $fields);

    $this->assertFileExists(app_path("Http/Controllers/{$name}Controller.php"));

    $content = file_get_contents(app_path("Http/Controllers/{$name}Controller.php"));

    $this->assertStringContainsString("class {$name}Controller extends BaseController", $content);
    $this->assertStringContainsString("protected \$table = '{$tableName}'", $content);
    $this->assertStringContainsString("'name' => 'required|string|max:255'", $content);
  }

  public function test_validation_rules_generation()
  {
    $fields = [
      ['fieldName' => 'email', 'fieldType' => 'string', 'nullable' => true, 'length' => 255],
      ['fieldName' => 'age', 'fieldType' => 'integer', 'nullable' => false, 'length' => null],
      ['fieldName' => 'active', 'fieldType' => 'boolean', 'nullable' => false, 'length' => null]
    ];

    $reflector = new \ReflectionClass(ControllerGenerator::class);
    $method = $reflector->getMethod('buildValidationRules');
    $method->setAccessible(true);

    $rules = $method->invoke($this->generator, $fields);

    $this->assertStringContainsString("'email' => 'nullable|string|max:255'", $rules);
    $this->assertStringContainsString("'age' => 'required|integer'", $rules);
    $this->assertStringContainsString("'active' => 'required|boolean'", $rules);
  }
}
