<?php


namespace DependencyAnalysis\Result;


class StdOutAnalysisResultPrinter implements AnalysisResultPrinter
{
    public function print(AnalysisResult $analysisResult): void
    {
        if ($analysisResult->isSuccess()) {
            fwrite(
                STDOUT,
                PHP_EOL .
                'All dependencies inside your project satisfy the approved dependency graph' . PHP_EOL
            );
        } else {
            fwrite(
                STDOUT,
                PHP_EOL .
                'You have problems with dependencies in your project' . PHP_EOL
            );
        }
    }

}