<?php

namespace Anturi\Larastarted\Traits;

trait FieldBuilderTrait
{
  protected function askForFields(): array
  {
    $fields = [];
    do {
      $fieldName = $this->ask("Nombre del campo (deja vacío para finalizar)");
      if ($fieldName) {
        $fieldType = $this->choice(
          "Selecciona el tipo de dato para '{$fieldName}'",
          ['string', 'integer', 'unsignedBigInteger', 'boolean', 'text', 'date', 'timestamps'],
          0
        );

        $length = null;
        if (in_array($fieldType, ['string', 'integer'])) {
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

    return $fields;
  }

  protected function askForRelations(): array
  {
    $relations = [];
    if ($this->confirm("¿Deseas agregar relaciones?", false)) {
      do {
        $relationField = $this->ask("Nombre del campo de la relación (ejemplo: user_id)");
        if ($relationField) {
          $reference = $this->ask("Campo referenciado (deja vacío para 'id')") ?: 'id';
          $referenceTable = $this->ask("Tabla referenciada (ejemplo: users)");
          $cascade = $this->confirm("¿Cascade ON DELETE y ON UPDATE?", true);

          $relations[] = [
            'relationField' => $relationField,
            'reference' => $reference,
            'referenceTable' => $referenceTable,
            'cascade' => $cascade
          ];
        }
      } while ($this->confirm("¿Deseas agregar otra relación?", false));
    }
    return $relations;
  }
}
