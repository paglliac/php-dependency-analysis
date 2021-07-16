<?php

namespace DependencyAnalysis;


use DependencyAnalysis\Config\Config;

class Analyzer
{
    public function analyze(Config $config): AnalysisResult
    {
        $analysisResult = new AnalysisResult();

        $fileIterator = new FileIterator($config->getPath());

        foreach ($fileIterator->next() as $file){
            $analysisResult->addAnalyzedFile();
        }


        return $analysisResult;
    }
}