<?php

namespace Tests\Unit\Generators;

use Anturi\Larastarted\Generators\ModelGenerator;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ModelGeneratorTest extends TestCase
{
  private ModelGenerator $generator;

  protected function setUp(): void
  {
    parent::setUp();
    $this->generator = new ModelGenerator();
  }

  protected function tearDown(): void
  {
    if (file_exists(app_path('Models/TestModel.php'))) {
      unlink(app_path('Models/TestModel.php'));
    }
    parent::tearDown();
  }

  public function test_generate_creates_model_file()
  {
    $modelName = 'TestModel';
    $tableName = 'test_models';
    $fields = [
      ['fieldName' => 'name'],
      ['fieldName' => 'email']
    ];
    $relations = [];

    $this->generator->generate($modelName, $tableName, $fields, $relations);

    $this->assertFileExists(app_path("Models/{$modelName}.php"));

    $content = file_get_contents(app_path("Models/{$modelName}.php"));

    $this->assertStringContainsString("class {$modelName} extends Model", $content);
    $this->assertStringContainsString("protected \$table = '{$tableName}'", $content);
    $this->assertStringContainsString("protected \$fillable = ['name', 'email']", $content);
  }

  public function test_generate_with_relations()
  {
    $modelName = 'Post';
    $tableName = 'posts';
    $fields = [['fieldName' => 'title']];
    $relations = [
      ['relationField' => 'user_id', 'reference' => 'id', 'referenceTable' => 'users']
    ];

    $this->generator->generate($modelName, $tableName, $fields, $relations);

    $content = file_get_contents(app_path("Models/{$modelName}.php"));

    $this->assertStringContainsString("public function user()", $content);
    $this->assertStringContainsString("\$this->belongsTo(User::class)", $content);
  }
}
