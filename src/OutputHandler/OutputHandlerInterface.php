<?php

declare(strict_types=1);

namespace DynamicDeadCodeDetector\OutputHandler;

/**
 * @author Martin Ziegler <mz33info@gmail.com>
 */
interface OutputHandlerInterface
{
    /**
     * @param class-string[] $data
     */
    public function save(array $data): void;
}
