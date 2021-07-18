<?php

namespace DependencyAnalysis;


use DependencyAnalysis\Config\Config;

class Analyzer
{
    public function analyze(Config $config): AnalysisResult
    {
        $analysisResult = new AnalysisResult();

        $fileIterator = new FileIterator($config->getPath());
        $fileParser = new FileParser();

        foreach ($fileIterator->next() as $file) {
            $parsedClass = $fileParser->parseFile($file->getPath() . '/' . $file->getFilename());

            if (!$parsedClass) {
                continue;
            }

            if ($error = $this->isFileSatisfyConfig($parsedClass, $config)) {
                $analysisResult->addCorrectFile($parsedClass);
            } else {
                $analysisResult->addIncorrectFile($parsedClass, $error);
            }
        }


        return $analysisResult;
    }

    private function isFileSatisfyConfig(ParsedClass $parsedClass, Config $config): bool
    {
        return $parsedClass && $config;
    }
}