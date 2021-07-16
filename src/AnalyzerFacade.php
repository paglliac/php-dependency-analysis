<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Config\PhpFileConfigParser;

class AnalyzerFacade
{
    public function run(string $configPath): bool
    {
        $configParser = new PhpFileConfigParser();
        $config = $configParser->parse($configPath);

        $analyzer = new Analyzer();
        $result = $analyzer->analyze($config);

        return $result->isSuccess();
    }

}