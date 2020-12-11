<?php
declare(strict_types=1);

namespace DocDoc\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

/**
 * Процессор выставялет поле _es_index, которое будет принятно лог коллектором для маршрутизации в нужный индекс ES
 */
class EsIndexProcessor implements ProcessorInterface
{
    protected string $prefix;

    public function __construct(string $prefix = 'dd-app-')
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(array $record)
    {
        if (!array_key_exists('_es_index', $record['context'])) {
            $record['context']['_es_index'] = array_key_exists('channel', $record)
                ? $record['channel']
                : 'default';
        }

        $record['_es_index'] = $this->prefix . $record['context']['_es_index'] . '-' . date('Y.m.d');
        unset($record['context']['_es_index']);

        return $record;
    }
}