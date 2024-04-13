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
        $fileHandle = fopen($this->targetFile, 'c+');

        try {
            $this->tryLockFile($fileHandle);

            $existingData = $this->getJsonContents($fileHandle);
            $updatedData = array_unique(array_merge($existingData, $data));

            $this->writeJsonContents($fileHandle, $updatedData);
        } catch (FileOutputException $e) {
            $this->logger?->error("Could not save data to file: {$e->getMessage()}", ['exception' => $e]);
        } finally {
            fclose($fileHandle);
        }
    }

    /**
     * @param resource $fileHandle
     * @throws FileOutputException
     */
    private function tryLockFile($fileHandle): void
    {
        $attempt = 0;
        while (!flock($fileHandle, LOCK_EX | LOCK_NB)) {
            $attempt++;
            if ($attempt >= self::ATTEMPTS) {
                throw FileOutputException::fromCannotLockFile($this->targetFile, self::ATTEMPTS);
            }

            usleep(500000);
        }
    }

    /**
     * @param resource $fileHandle
     * @return string[]
     * @throws FileOutputException
     */
    private function getJsonContents($fileHandle): array
    {
        $rawData = stream_get_contents($fileHandle);
        /** @var string[]|null $decodedData */
        $decodedData = json_decode($rawData, true);
        if (!is_array($decodedData)) {
            return [];
        }

        return $decodedData;
    }

    /**
     * @param resource $fileHandle
     * @param string[] $data
     */
    private function writeJsonContents($fileHandle, array $data): void
    {
        ftruncate($fileHandle, 0);
        rewind($fileHandle);
        fwrite($fileHandle, json_encode($data));
        fflush($fileHandle);
        flock($fileHandle, LOCK_UN);
    }
}
