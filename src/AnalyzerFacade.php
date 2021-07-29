<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Config\PhpFileConfigParser;
use DependencyAnalysis\Result\AnalysisResult;
use DependencyAnalysis\Result\ResultPrinterFactory;

class AnalyzerFacade
{
    public function run(string $configPath, array $filesFilter = []): AnalysisResult
    {
        $configParser = new PhpFileConfigParser();
        $config = $configParser->parse($configPath);

        $analyzer = new Analyzer();
        $result = $analyzer->analyze($config, $filesFilter);

        $resultPrinter = (new ResultPrinterFactory())->make($config);
        $resultPrinter->print($result);

        return $result;
    }

}