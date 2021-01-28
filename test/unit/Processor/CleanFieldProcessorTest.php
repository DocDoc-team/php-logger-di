<?php
declare(strict_types=1);

use DocDoc\Logger\Processor\CleanFieldProcessor;
use PHPUnit\Framework\TestCase;

class CleanFieldProcessorTest extends TestCase
{
    public function testProcessor(): void
    {
        $processor = new CleanFieldProcessor(['_temp']);
        $recordSrc = [
            '_temp' => 123,
            '_telegram' => true,
            'message' => 'test'
        ];

        $record = $processor($recordSrc);
        $expected = [
            '_telegram' => true,
            'message' => 'test'
        ];
        static::assertSame($expected, $record);

        $processor->addField('_telegram');
        $record = $processor($recordSrc);
        $expected = [
            'message' => 'test'
        ];
        static::assertSame($expected, $record);
    }
}