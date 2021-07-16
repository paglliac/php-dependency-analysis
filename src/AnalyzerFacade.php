<?php


namespace DependencyAnalysis;


class AnalyzerFacade
{
    public function run(string $configPath): bool
    {
        $configParser = new ConfigParser();
        $config = $configParser->parse($configPath);

        $analyzer = new Analyzer();
        $result = $analyzer->analyze($config);

        return $result->isSuccess();
    }

}