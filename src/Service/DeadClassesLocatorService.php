<?php

declare(strict_types=1);

namespace DynamicDeadCodeDetector\Service;

use DynamicDeadCodeDetector\Service\DeadClassesLocatorService\DeadClassLocationException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * @author Martin Ziegler <mz33info@gmail.com>
 */
final class DeadClassesLocatorService
{
    private const NAMESPACE_CLASSNAME_PATTERN = '/namespace\s+([^;]*);.*?class\s+(\w+)/si';

    public function __construct(
        private readonly string $srcPath,
        private readonly string $usedClassesPath
    ) {}

    /**
     * @return string[]
     * @throws DeadClassLocationException
     */
    public function getDeadClasses(): array
    {
        $allClasses = $this->getAllClasses();
        $usedClasses = $this->getUsedClasses();

        return array_values(array_diff($allClasses, $usedClasses));
    }

    /**
     * @return string[]
     */
    private function getAllClasses(): array
    {
        $fileIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->srcPath));

        $classes = [];
        /** @var SplFileInfo $file */
        foreach ($fileIterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $content = file_get_contents($file->getRealPath());
            preg_match_all(self::NAMESPACE_CLASSNAME_PATTERN, $content, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $namespace = trim($match[1]);
                $className = $match[2];
                $fullName = $namespace ? ($namespace . '\\' . $className) : $className;

                $classes[] = $fullName;
            }
        }

        return $classes;
    }

    /**
     * @return string[]
     * @throws DeadClassLocationException
     */
    private function getUsedClasses(): array
    {
        $jsonContent = file_get_contents($this->usedClassesPath);
        /** @var ?string[] $declaredClasses */
        $declaredClasses = json_decode($jsonContent, true);

        if ($declaredClasses === null) {
            throw DeadClassLocationException::fromCouldNotLoadUsedClasses($this->usedClassesPath);
        }

        return $declaredClasses;
    }
}
