<?php

namespace Tests\Unit\Traits;

use Anturi\Larastarted\Commands\GenerateResourcesCommand;
use Tests\TestCase;

class FieldBuilderTraitTest extends TestCase
{
  private $command;

  protected function setUp(): void
  {
    parent::setUp();
    $this->command = new class extends GenerateResourcesCommand {
      // Clase anónima para testear el trait
      public function callAskForFields()
      {
        return $this->askForFields();
      }

      public function callAskForRelations()
      {
        return $this->askForRelations();
      }
    };
  }

  public function test_ask_for_fields()
  {
    $this->command->setInputs([
      'name',
      'string',
      '',
      'no', // Campo name
      'age',
      'integer',
      '3',
      'yes', // Campo age
      '', // Finalizar
    ]);

    $fields = $this->command->callAskForFields();

    $this->assertCount(2, $fields);
    $this->assertEquals('name', $fields[0]['fieldName']);
    $this->assertEquals('string', $fields[0]['fieldType']);
    $this->assertFalse($fields[0]['nullable']);

    $this->assertEquals('age', $fields[1]['fieldName']);
    $this->assertEquals('integer', $fields[1]['fieldType']);
    $this->assertTrue($fields[1]['nullable']);
  }

  public function test_ask_for_relations()
  {
    $this->command->setInputs([
      'yes', // ¿Agregar relaciones?
      'user_id',
      'id',
      'users',
      'yes', // Relación 1
      'no', // ¿Otra relación?
    ]);

    $relations = $this->command->callAskForRelations();

    $this->assertCount(1, $relations);
    $this->assertEquals('user_id', $relations[0]['relationField']);
    $this->assertEquals('users', $relations[0]['referenceTable']);
    $this->assertTrue($relations[0]['cascade']);
  }
}
