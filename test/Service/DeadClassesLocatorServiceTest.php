<?php

namespace DynamicDeadCodeDetector\Service;

use DynamicDeadCodeDetector\Service\TestClasses\DeadClass;
use DynamicDeadCodeDetector\Service\TestClasses\UsedClass;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @author Martin Ziegler <mz33info@gmail.com>
 */
class DeadClassesLocatorServiceTest extends TestCase
{
    public function test_getDeadClasses(): void
    {
        $usedClasses = [UsedClass::class];
        $srcPath = __DIR__ . '/TestClasses';

        vfsStream::setup('root');
        $virtualFilePath = vfsStream::url('root/used.json');
        file_put_contents($virtualFilePath, json_encode($usedClasses));

        $service = new DeadClassesLocatorService($srcPath, $virtualFilePath);

        $deadClasses = $service->getDeadClasses();

        $this->assertEquals([DeadClass::class], $deadClasses);
    }

    public function test_getDeadClassesFromUsedClasses_emptyUsedClasses(): void
    {
        $usedClasses = [];
        $srcPath = __DIR__ . '/TestClasses';

        $service = new DeadClassesLocatorService($srcPath, '');

        $deadClasses = $service->getDeadClassesFromUsedClasses($usedClasses);

        $this->assertEquals([UsedClass::class, DeadClass::class], $deadClasses);
    }

    public function test_getDeadClassesFromUsedClasses_subsetUsedClasses(): void
    {
        $usedClasses = [UsedClass::class];
        $srcPath = __DIR__ . '/TestClasses';

        $service = new DeadClassesLocatorService($srcPath, '');

        $deadClasses = $service->getDeadClassesFromUsedClasses($usedClasses);

        $this->assertEquals([DeadClass::class], $deadClasses);
    }

    public function test_getDeadClassesFromUsedClasses_supersetUsedClasses(): void
    {
        $usedClasses = [UsedClass::class, DeadClass::class, 'SomeOtherClass'];
        $srcPath = __DIR__ . '/TestClasses';

        $service = new DeadClassesLocatorService($srcPath, '');

        $deadClasses = $service->getDeadClassesFromUsedClasses($usedClasses);

        $this->assertEquals([], $deadClasses);
    }

    public function test_getDeadClassesFromUsedClasses_someUsedClasses(): void
    {
        $usedClasses = [UsedClass::class, 'SomeOtherClass'];
        $srcPath = __DIR__ . '/TestClasses';

        $service = new DeadClassesLocatorService($srcPath, '');

        $deadClasses = $service->getDeadClassesFromUsedClasses($usedClasses);

        $this->assertEquals([DeadClass::class], $deadClasses);
    }
}
