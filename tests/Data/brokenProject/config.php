<?php

return [
    'dependencies' => [
        '\Domain' => null,
        '\Application' => ['\Domain'],
        '\Infrastructure' => ['\Domain']
    ],
    'path' => __DIR__,
    'fail_on_non_presented_namespace' => false,
    'php_version' => PhpParser\ParserFactory::PREFER_PHP7,
    'allowed_extensions' => ['php'],
    'output' => \DependencyAnalysis\Result\NullResultPrinter::class,
];
