<?php
declare(strict_types=1);

use DocDoc\Logger\Processor\CleanContextProcessor;
use PHPUnit\Framework\TestCase;

class CleanContextProcessorTest extends TestCase
{
    public function testProcessor(): void
    {
        $processor = new CleanContextProcessor(['_temp']);
        $recordSrc = [
            'context' => [
                '_temp' => 123,
                '_telegram' => true,
                'message' => 'test'
            ]
        ];

        $record = $processor($recordSrc);
        $expected = [
            'context' => [
                '_telegram' => true,
                'message' => 'test'
            ],
        ];
        static::assertSame($expected, $record);

        $processor->addField('_telegram');
        $record = $processor($recordSrc);
        $expected = [
            'context' => [
                'message' => 'test'
            ],
        ];
        static::assertSame($expected, $record);
    }
}