<?php

declare(strict_types=1);

namespace DynamicDeadCodeDetector\OutputHandler;

use DynamicDeadCodeDetector\OutputHandler\FileOutputHandler\FileOutputException;

/**
 * @author Martin Ziegler <mz33info@gmail.com>
 */
final class FileOutputHandler implements OutputHandlerInterface
{
    private const ATTEMPTS = 5;

    /**
     * @var ?\Monolog\Logger
     */
    private $logger;

    /**
     * @param string $targetFile
     * @param ?\Monolog\Logger $logger
     */
    public function __construct(
        private readonly string $targetFile,
        $logger = null,
    ) {
        $this->logger = $logger;
    }

    /**
     * @param class-string[] $data
     */
    public function save(array $data): void
    {
        $lockHandle = null;

        try {
            $lockHandle = $this->tryLockFile();

            $existingData = $this->getFileContents();
            $newData = array_values(array_diff($data, $existingData));

            $this->writeFileContents($newData);
        } catch (FileOutputException $e) {
            $this->logger?->error("Could not save data to file: {$e->getMessage()}", ['exception' => $e]);
        } finally {
            if ($lockHandle !== null) {
                $this->unlockLockFile($lockHandle);
            }
        }
    }

    /**
     * @throws FileOutputException
     * @return resource
     */
    private function tryLockFile()
    {
        $lockFileHandle = fopen($this->getLockFile(), 'c+');

        $attempt = 0;
        while (!flock($lockFileHandle, LOCK_EX | LOCK_NB)) {
            $attempt++;
            if ($attempt >= self::ATTEMPTS) {
                throw FileOutputException::fromCannotLockFile($this->targetFile, self::ATTEMPTS);
            }

            usleep(500000);
        }

        return $lockFileHandle;
    }

    /**
     * @param resource $fileHandle
     */
    private function unlockLockFile($fileHandle): void
    {
        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    }

    /**
     * @return string[]
     */
    private function getFileContents(): array
    {
        $contents = [];
        if (file_exists($this->targetFile)) {
            $contents = file($this->targetFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }

        return $contents;
    }

    /**
     * @param string[] $data
     */
    private function writeFileContents(array $data): void
    {
        if (!empty($data)) {
            file_put_contents($this->targetFile, implode(PHP_EOL, $data) . PHP_EOL, FILE_APPEND);
        }
    }

    private function getLockFile(): string
    {
        return $this->targetFile . '.lock';
    }
}
