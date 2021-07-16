<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Config\PhpFileConfigParser;

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