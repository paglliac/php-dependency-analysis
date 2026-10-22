<?php


namespace DependencyAnalysis\Result;


use DependencyAnalysis\Config\Config;

class ResultPrinterFactory
{
    public function make(Config $config): AnalysisResultPrinter
    {
        // `output` is optional in the config file; default to stdout.
        if ($config->getOutput() === null || $config->getOutput() === StdOutAnalysisResultPrinter::class) {
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