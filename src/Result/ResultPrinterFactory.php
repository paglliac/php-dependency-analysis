<?php


namespace DependencyAnalysis\Result;


use DependencyAnalysis\Config\Config;

class ResultPrinterFactory
{
    public function make(Config $config): AnalysisResultPrinter
    {
        if ($config->getOutput() === StdOutAnalysisResultPrinter::class) {
            return new StdOutAnalysisResultPrinter();
        }

        if ($config->getOutput() === FileAnalysisResultPrinter::class) {
            return new FileAnalysisResultPrinter($config->getOutputPath());
        }

        if ($config->getOutput() === NullResultPrinter::class) {
            return new NullResultPrinter();
        }

        throw new \RuntimeException("Unexpected result printer {$config->getOutput()}");
    }
}