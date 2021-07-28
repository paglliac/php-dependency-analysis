<?php


namespace DependencyAnalysis\Result;


class FileAnalysisResultPrinter extends StdOutAnalysisResultPrinter implements AnalysisResultPrinter
{
    private string $outputFilePath;

    public function __construct(string $outputFilePath)
    {
        $this->outputFilePath = $outputFilePath;
    }

    public function print(AnalysisResult $analysisResult): void
    {
        $this->clearFile();
        parent::print($analysisResult);
    }

    protected function writeLine(string $line): void
    {
        file_put_contents($this->outputFilePath, $line, FILE_APPEND);
    }

    private function clearFile()
    {
        file_put_contents($this->outputFilePath, '');
    }
}