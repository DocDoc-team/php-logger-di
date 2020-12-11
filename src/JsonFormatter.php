<?php
declare(strict_types=1);

namespace DocDoc\Logger;

use Monolog\Formatter\NormalizerFormatter;

class JsonFormatter extends NormalizerFormatter
{
    public function __construct()
    {
        parent::__construct('Y-m-d\TH:i:s.up');
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $formatted = parent::format($record);
        $formatted['ts'] = $formatted['datetime'];

        foreach ($formatted['context'] as $name => $value) {
            $formatted["ctx_$name"] = $value;
        }

        foreach ($formatted['extra'] as $name => $value) {
            $formatted["extra_$name"] = $value;
        }

        unset($formatted['context'], $formatted['extra'], $formatted['datetime'], $formatted['level_name'],
            $formatted['channel']
        );

        return $formatted;
    }
}
