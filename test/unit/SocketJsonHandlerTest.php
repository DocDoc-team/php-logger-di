<?php
declare(strict_types=1);

use DocDoc\Logger\Processor\DateProcessor;
use DocDoc\SymfonyDiLoader\LoaderContainer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SocketJsonHandlerTest extends TestCase
{
    protected string $cacheFile;

    protected const HOST = '127.0.0.1';
    protected const PORT = 12201;

    protected static $serverSocket;

    public static function setUpBeforeClass(): void
    {
        self::$serverSocket = self::createTempUdpServer();
    }

    protected static function shutdownUdpServer(): void
    {
        socket_close(self::$serverSocket);
        self::$serverSocket = null;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->cacheFile = __DIR__ . '/var/cache.php';
    }

    public function tearDown(): void
    {
        parent::tearDown();
        @unlink($this->cacheFile);
    }

    protected static function createTempUdpServer()
    {
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_bind($socket, self::HOST, self::PORT);
        return $socket;
    }

    protected function getMessageFromTempSocket(): string
    {
        $buffer = '';
        $message = '';

        while (socket_recv(self::$serverSocket, $buffer, 8192, MSG_DONTWAIT)) {
            $message .= $buffer;
        }

        return $message;
    }

    public function testWriteToSocketHandler(): void
    {
        $container = $this->getContainer();
        $logger = $container->get(LoggerInterface::class);
        $logger->pushProcessor(new DateProcessor);

        $now = new DateTime;
        $logger->error('test error', [
            'datetime' => $now,
            'test' => true,
            'array' => [1, 3, 4],
            'object' => [
                'name' => 'alex',
                'age' => 31
            ]
        ]);

        $actual = $this->getMessageFromTempSocket();
        $expected = '{"message":"test error","level":400,"channel":"default","_es_index":"dd-app-default-' . $now->format('Y.m.d') . '","ts":"' . $now->format('Y-m-d\TH:i:s.up') . '","ctx_test":true,"ctx_array":[1,3,4],"ctx_object":{"name":"alex","age":31},"debug_trace":[".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php:1526 :: SocketJsonHandlerTest->testWriteToSocketHandler",".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php:1132 :: PHPUnit\\\Framework\\\TestCase->runTest",".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestResult.php:722 :: PHPUnit\\\Framework\\\TestCase->runBare",".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php:884 :: PHPUnit\\\Framework\\\TestResult->run",".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php:677 :: PHPUnit\\\Framework\\\TestCase->run",".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php:677 :: PHPUnit\\\Framework\\\TestSuite->run",".\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php:677 :: PHPUnit\\\Framework\\\TestSuite->run",".\/vendor\/phpunit\/phpunit\/src\/TextUI\/TestRunner.php:667 :: PHPUnit\\\Framework\\\TestSuite->run",".\/vendor\/phpunit\/phpunit\/src\/TextUI\/Command.php:142 :: PHPUnit\\\TextUI\\\TestRunner->run",".\/vendor\/phpunit\/phpunit\/src\/TextUI\/Command.php:95 :: PHPUnit\\\TextUI\\\Command->run",".\/vendor\/phpunit\/phpunit\/phpunit:61 :: PHPUnit\\\TextUI\\\Command::main"]}';
        self::assertSame($expected, $actual);
    }

    protected function getContainer(): ContainerInterface
    {
        $loader = new LoaderContainer;
        return $loader->getContainer([dirname(__DIR__, 2) . '/src/logger.yml'], $this->cacheFile);
    }
}