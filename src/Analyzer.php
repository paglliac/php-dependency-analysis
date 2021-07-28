<?php

namespace DependencyAnalysis;


use DependencyAnalysis\Config\Config;
use DependencyAnalysis\Parser\FileParser;
use DependencyAnalysis\Parser\ParsedClass;
use DependencyAnalysis\Result\AnalysisResult;

class Analyzer
{
    public function analyze(Config $config): AnalysisResult
    {
        $analysisResult = new AnalysisResult();

        $fileIterator = new FileIterator($config->getPath(), $config->getAllowedExtensions());
        $fileParser = new FileParser($config->getPhpVersion());

        foreach ($fileIterator->next() as $file) {
            $parsedClass = $fileParser->parseFile($file->getPath() . '/' . $file->getFilename());

            if (!$parsedClass) {
                continue;
            }

            $errors = $this->isFileSatisfyConfig($parsedClass, $config->getDependencyGraph());
            if (count($errors) === 0) {
                $analysisResult->addCorrectFile($parsedClass);
            } else {
                $analysisResult->addIncorrectFile($parsedClass, $errors);
            }
        }


        return $analysisResult;
    }

    private function isFileSatisfyConfig(ParsedClass $parsedClass, DependencyGraph $dependencyGraph): array
    {
        return $dependencyGraph->isSatisfy($parsedClass);
    }
}