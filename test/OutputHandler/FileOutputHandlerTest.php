<?php

namespace DynamicDeadCodeDetector\OutputHandler;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @author Martin Ziegler <mz33info@gmail.com>
 */
class FileOutputHandlerTest extends TestCase
{
    public function test_save(): void
    {
        vfsStream::setup('root');
        $virtualFilePath = vfsStream::url('root/test.json');

        $outputHandler = new FileOutputHandler($virtualFilePath);
        $outputHandler->save(['Foo', 'Bar']);

        $this->assertEquals('["Foo","Bar"]', file_get_contents($virtualFilePath));
    }
}
