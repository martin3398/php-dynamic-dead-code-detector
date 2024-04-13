<?php

declare(strict_types=1);

namespace DynamicDeadCodeDetector\Service;

use DynamicDeadCodeDetector\OutputHandler\OutputHandlerInterface;

/**
 * @author Martin Ziegler <mz33info@gmail.com>
 */
final class ShutdownFunctionService
{
    public function __construct(
        private readonly OutputHandlerInterface $outputHandler,
        private readonly bool $isFpm = true,
    ) {}

    public function register(): void
    {
        register_shutdown_function([self::class, 'shutdownHandler']);
    }

    public function shutdownHandler(): void
    {
        if ($this->isFpm) {
            fastcgi_finish_request();
        }

        $declaredClasses = get_declared_classes();

        $this->outputHandler->save($declaredClasses);
    }
}
