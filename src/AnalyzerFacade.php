<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Config\PhpFileConfigParser;
use DependencyAnalysis\Result\AnalysisResult;

class AnalyzerFacade
{
    public function run(string $configPath): AnalysisResult
    {
        $configParser = new PhpFileConfigParser();
        $config = $configParser->parse($configPath);

        $analyzer = new Analyzer();

        return $analyzer->analyze($config);
    }

}