<?php
declare(strict_types=1);

namespace DocDoc\Logger\Processor;


/**
 * Удаляет поля из root лога (не контекста)
 */
class CleanFieldProcessor extends CleanContextProcessor
{

    protected function cleanField(string $field, array $record): array
    {
        $value = $record[$field] ?? null;
        if ($value) {
            unset($record[$field]);
        }

        return $record;
    }
}
