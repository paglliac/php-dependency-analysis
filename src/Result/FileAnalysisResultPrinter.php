<?php


namespace DependencyAnalysis\Result;


class FileAnalysisResultPrinter implements AnalysisResultPrinter
{
    private string $outputFilePath;

    public function __construct(string $outputFilePath)
    {
        $this->outputFilePath = $outputFilePath;
    }


    public function print(AnalysisResult $analysisResult): void
    {
        if ($analysisResult->isSuccess()) {
            file_put_contents($this->outputFilePath, 'All dependencies inside your project satisfy the approved dependency graph');
        } else {
            file_put_contents($this->outputFilePath, 'You have problems with dependencies in your project');
        }
    }
}