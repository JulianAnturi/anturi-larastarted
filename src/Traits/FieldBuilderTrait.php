<?php

namespace Anturi\Larastarted\Traits;

trait FieldBuilderTrait
{
  protected function askForFields(): array
  {
    $fields = [];
    do {
      $fieldName = $this->ask("Field name (leave empty to finish)");
      if ($fieldName) {
        $fieldType = $this->choice(
          "Select the data type for '{$fieldName}'",
          ['string', 'integer', 'unsignedBigInteger', 'boolean', 'text', 'date'],
          0
        );

        $length = null;
        if (in_array($fieldType, ['string', 'integer'])) {
          $length = $this->ask("Length for '{$fieldName}'? (leave empty for default)");
          $length = is_numeric($length) ? (int) $length : null;
        }

        $isNullable = $this->confirm("Can the field '{$fieldName}' be nullable?", false);

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
    if ($this->confirm("Do you want to add relations?", false)) {
      do {
        $relationField = $this->ask("Relation field name (e.g., user_id)");
        if ($relationField) {
          $reference = $this->ask("Referenced field (leave empty for 'id')") ?: 'id';
          $referenceTable = $this->ask("Referenced table (e.g., users)");
          $cascade = $this->confirm("Cascade ON DELETE and ON UPDATE?", true);

          $relations[] = [
            'relationField' => $relationField,
            'reference' => $reference,
            'referenceTable' => $referenceTable,
            'cascade' => $cascade
          ];
        }
      } while ($this->confirm("Do you want to add another relation?", false));
    }
    return $relations;
  }
}
