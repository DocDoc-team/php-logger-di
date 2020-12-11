<?php
declare(strict_types=1);

use DocDoc\Logger\Processor\EsIndexProcessor;
use PHPUnit\Framework\TestCase;

class EsIndexProcessorTest extends TestCase
{
    public function testProcessor(): void
    {
        $prefix = 'prefix-';
        $processor = new EsIndexProcessor($prefix);

        # custom index
        $record = $processor([
            'context' => [
                '_es_index' => 'custom_index'
            ]
        ]);
        $expected = [
            'context' => [],
            '_es_index' => $prefix . 'custom_index-' . date('Y.m.d'),
        ];
        static::assertSame($expected, $record);

        # withoug channel
        $record = $processor([
            'context' => []
        ]);
        $expected = [
            'context' => [],
            '_es_index' => $prefix . 'default-' . date('Y.m.d'),
        ];
        static::assertSame($expected, $record);

        # with channel
        $record = $processor([
            'context' => [],
            'channel' => 'app',
        ]);
        $expected = [
            'context' => [],
            'channel' => 'app',
            '_es_index' => $prefix . 'app-' . date('Y.m.d'),
        ];
        static::assertSame($expected, $record);
    }
}