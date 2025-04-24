<?php

namespace Tests\Unit\Generators;

use Anturi\Larastarted\Generators\MigrationGenerator;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
  private MigrationGenerator $generator;

  protected function setUp(): void
  {
    parent::setUp();
    $this->generator = new MigrationGenerator();
  }

  protected function tearDown(): void
  {
    // Limpiar archivos de prueba
    array_map('unlink', glob(database_path('migrations/*.php')));
    parent::tearDown();
  }

  public function test_generate_creates_migration_file()
  {
    $tableName = 'test_table';
    $fields = [
      ['fieldName' => 'name', 'fieldType' => 'string', 'nullable' => false, 'length' => 255],
      ['fieldName' => 'age', 'fieldType' => 'integer', 'nullable' => true, 'length' => null]
    ];
    $relations = [];

    $result = $this->generator->generate($tableName, $fields, $relations);

    // Verificar que se creó el archivo
    $migrationFiles = glob(database_path('migrations/*_create_' . $tableName . '_table.php'));
    $this->assertCount(1, $migrationFiles);

    // Verificar contenido del archivo
    $content = file_get_contents($migrationFiles[0]);
    $this->assertStringContainsString("Schema::create('{$tableName}'", $content);
    $this->assertStringContainsString("\$table->string('name', 255)", $content);
    $this->assertStringContainsString("\$table->integer('age')->nullable()", $content);

    // Verificar return values
    $this->assertEquals($fields, $result['fields']);
    $this->assertEquals($relations, $result['relations']);
  }

  public function test_generate_with_relations()
  {
    $tableName = 'posts';
    $fields = [
      ['fieldName' => 'title', 'fieldType' => 'string', 'nullable' => false, 'length' => 255]
    ];
    $relations = [
      [
        'relationField' => 'user_id',
        'reference' => 'id',
        'referenceTable' => 'users',
        'cascade' => true
      ]
    ];

    $result = $this->generator->generate($tableName, $fields, $relations);

    $migrationFiles = glob(database_path('migrations/*_create_' . $tableName . '_table.php'));
    $content = file_get_contents($migrationFiles[0]);

    $this->assertStringContainsString("\$table->unsignedBigInteger('user_id')", $content);
    $this->assertStringContainsString(
      "\$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade')",
      $content
    );

    // Verificar que se agregó el campo de relación a fields
    $this->assertCount(2, $result['fields']);
    $this->assertEquals('user_id', $result['fields'][1]['fieldName']);
  }
}
