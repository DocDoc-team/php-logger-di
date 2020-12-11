<?php
declare(strict_types=1);

namespace DocDoc\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

/**
 * Удаляет из контектса поля
 */
class CleanContextProcessor implements ProcessorInterface
{

    protected array $fields = [];

    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    public function addField(string $name): self
    {
        if (in_array($name, $this->fields, true) === false) {
            $this->fields[] = $name;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(array $record)
    {
        foreach ($this->fields as $field) {
            $record = $this->cleanField($field, $record);
        }

        return $record;
    }

    protected function cleanField(string $field, array $record): array
    {
        $value = $record['context'][$field] ?? null;
        if ($value) {
            unset($record['context'][$field]);
        }

        return $record;
    }
}
