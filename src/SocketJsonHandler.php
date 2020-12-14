<?php
declare(strict_types=1);

namespace DocDoc\Logger;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\SocketHandler;
use Monolog\Logger;

class SocketJsonHandler extends SocketHandler
{
    protected int $jsonFlag = JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE;

    /**
     * @param array $params
     * @param int $level
     * @param bool $bubble
     */
    public function __construct(array $params, $level = Logger::DEBUG, bool $bubble = true)
    {
        $connectionString = $params['address'] ?? 'udp://127.0.0.1:12201';
        parent::__construct($connectionString, $level, $bubble);

        $connectTimeout = $params['connectTimeout'] ?? 0;
        if ($connectTimeout) {
            $this->setConnectionTimeout($connectTimeout);
        }

        $writeTimeout = $params['connectTimeout'] ?? 0;
        if ($writeTimeout) {
            $this->setWritingTimeout($writeTimeout);
        }
    }

    protected function generateDataStream(array $record): string
    {
        return json_encode($record['formatted'], $this->jsonFlag);
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new JsonFormatter;
    }
}
