<?php
declare(strict_types=1);

use DocDoc\Logger\Processor\CleanContextProcessor;
use DocDoc\Logger\Processor\EsIndexProcessor;
use DocDoc\Logger\SocketJsonHandler;
use DocDoc\SymfonyDiLoader\LoaderContainer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceDiTest extends TestCase
{
    protected string $cacheFile;


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

    protected function getContainer(): ContainerInterface
    {
        $loader = new LoaderContainer;
        return $loader->getContainer([dirname(__DIR__, 2) . '/src/logger.yml'], $this->cacheFile);
    }

    public function testCreateLoggerFromYaml(): void
    {
        $container = $this->getContainer();

        static::assertInstanceOf(ContainerInterface::class, $container);
        static::assertInstanceOf(\Psr\Container\ContainerInterface::class, $container);

        $logger = $container->get(LoggerInterface::class);
        static::assertInstanceOf(LoggerInterface::class, $logger);

        $handlers = $logger->getHandlers();
        static::assertCount(1, $handlers);

        $socketJsonHandler = $handlers[0];
        static::assertInstanceOf(SocketJsonHandler::class, $socketJsonHandler);
    }

    public function testProcessorOrderOnHandler(): void
    {
        $container = $this->getContainer();
        $logger = $container->get(LoggerInterface::class);

        $handler = $logger->getHandlers()[0];
        $prop = new ReflectionProperty(SocketJsonHandler::class, 'processors');
        $prop->setAccessible(true);
        $processors = $prop->getValue($handler);

        static::assertCount(2, $processors);
        static::assertInstanceOf(EsIndexProcessor::class, $processors[0]);
        static::assertInstanceOf(CleanContextProcessor::class, $processors[1]);
    }
}