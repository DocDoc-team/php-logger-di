<?php
declare(strict_types=1);

use DocDoc\Logger\JsonFormatter;
use PHPUnit\Framework\TestCase;

class JsonFormatterTest extends TestCase
{
    public function testFormatter(): void
    {
        $formatter = new JsonFormatter;
        $now = new DateTime;
        $recordSrc = [
            'channel' => 'default',
            'datetime' => $now->format('Y-m-d\TH:i:s.up'),
            'context' => [
                '_telegram' => true,
                'message' => 'test'
            ],
            'extra' => [],
        ];

        $formatted = $formatter->format($recordSrc);
        $expected = [
            'channel' => 'default',
            'ts' => $now->format('Y-m-d\TH:i:s.up'),
            'ctx__telegram' => true,
            'ctx_message' => 'test',
        ];
        static::assertSame($expected, $formatted);
    }
}