# PHP Dynamic Dead Code Detector

This library can be used to dynamically (during runtime) detect dead classes in PHP applications.

## Usage

### How to Activate during Runtime
You can use the `ShutdownFunctionService` to register a shutdown function that will be called when the script ends. This function will then analyze the classes that were loaded during the script execution.

```php
use DynamicDeadCodeDetector\Service\ShutdownFunctionService;
use DynamicDeadCodeDetector\OutputHandler\FileOutputHandler;

$usedClassesFile = 'used_classes.json';
$fileOutputHandler = new FileOutputHandler($usedClassesFile);
$shutdownFunctionService = new ShutdownFunctionService($fileOutputHandler);
$shutdownFunctionService->register();
```

### How to find Dead Classes

Once we have the list of used classes, we can use the `DeadCodeDetector` to find the dead classes.

```php
use DynamicDeadCodeDetector\Service\DeadClassesLocatorService;

$usedClassesFile = 'used_classes.json';
$deadClassesLocator = new DeadClassesLocatorService('./src', $usedClassesFile);
$deadClasses = $deadClassesLocator->getDeadClasses();

foreach ($deadClasses as $deadClass) {
    echo $deadClass . PHP_EOL;
}
echo count($deadClasses) . ' dead classes found' . PHP_EOL;
```
