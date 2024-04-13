<?php

namespace DynamicDeadCodeDetector\OutputHandler\FileOutputHandler;

use Exception;

final class FileOutputException extends Exception
{
    public static function fromCannotLockFile(string $file, int $attempts): self
    {
        return new self(sprintf('Cannot lock file "%s" after %d attempts.', $file, $attempts));
    }
}
