<?php

namespace DynamicDeadCodeDetector\Service;

use DynamicDeadCodeDetector\OutputHandler\OutputHandlerInterface;
use PHPUnit\Framework\TestCase;

class ShutdownFunctionServiceTest extends TestCase
{
    protected function setUp(): void
    {
        require_once 'get_declared_classes.php';
    }

    public function test_shutdownHandler_calls_save_on_outputHandler()
    {
        $outputHandler = $this->createMock(OutputHandlerInterface::class);
        $outputHandler->expects($this->once())
            ->method('save')
            ->with(['Foo', 'Bar']);

        $shutdownFunctionService = new ShutdownFunctionService($outputHandler, false);
        $shutdownFunctionService->shutdownHandler();
    }
}
