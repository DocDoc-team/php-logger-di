<?php
declare(strict_types=1);

namespace DocDoc\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

/**
 * Переопределяет время записи, временем из контекста
 * В тестах позволяет фиксировать время создания записи
 */
class DateProcessor implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(array $record)
    {
        if (array_key_exists('datetime', $record['context'])) {
            $record['datetime'] = $record['context']['datetime'];
            unset($record['context']['datetime']);
        }

        return $record;
    }
}