<?php

namespace DynamicDeadCodeDetector\Service\DeadClassesLocatorService;

use Exception;

class DeadClassLocationException extends Exception
{
    public static function fromCouldNotLoadUsedClasses(string $usedClassesPath): self
    {
        return new self(sprintf('Could not load used classes from file: %s', $usedClassesPath));
    }
}
