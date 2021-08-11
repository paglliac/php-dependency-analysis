<?php

use DependencyAnalysis\Result\StdOutAnalysisResultPrinter;

return [
    'dependencies' => [
        '\DependencyAnalysis\Parser' => [],
        '\DependencyAnalysis\Config' => [
            '\DependencyAnalysis\DependencyGraph',
            '\DependencyAnalysis\Result\AnalysisResultPrinter'
        ],
        '\DependencyAnalysis\Result' => [
            '\DependencyAnalysis\Parser\ParsedClass',
            '\DependencyAnalysis\Config\Config'
        ],
    ],
    'valid_for_all' => [
        '\RuntimeException',
        '\Error',
        '\SplFileInfo'
    ],
    'path' => __DIR__ . '/src',
    'fail_on_non_presented_namespace' => false,
    'php_version' => PhpParser\ParserFactory::PREFER_PHP7,
    'allowed_extensions' => ['php'],
    'output' => StdOutAnalysisResultPrinter::class,
    'skip_not_readable_files' => false
];
