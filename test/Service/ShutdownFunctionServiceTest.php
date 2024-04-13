<?php

namespace Test\Service;

use DynamicDeadCodeDetector\OutputHandler\OutputHandlerInterface;
use DynamicDeadCodeDetector\Service\ShutdownFunctionService;
use PHPUnit\Framework\TestCase;

class ShutdownFunctionServiceTest extends TestCase
{

    function test_shutdownHandler_calls_save_on_outputHandler()
    {
        $outputHandler = $this->createMock(OutputHandlerInterface::class);
        $outputHandler->expects($this->once())
            ->method('save');

        $shutdownFunctionService = new ShutdownFunctionService($outputHandler, false);
        $shutdownFunctionService->shutdownHandler();
    }
}
