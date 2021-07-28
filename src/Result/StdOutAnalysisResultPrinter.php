<?php


namespace DependencyAnalysis\Result;


class StdOutAnalysisResultPrinter implements AnalysisResultPrinter
{
    public function print(AnalysisResult $analysisResult): void
    {
        $this->writeEmptyLine();
        $this->writeLine("Have been analyzed {$analysisResult->analyzedFilesAmount()} files");

        if ($analysisResult->isSuccess()) {
            $this->writeLine('All dependencies inside your project satisfy the approved dependency graph');
            $this->writeEmptyLine();
        } else {
            $this->writeLine("You have dependency problems in {$analysisResult->countIncorrectFiles()} files in your project:");
            $this->writeEmptyLine();

            foreach ($analysisResult->getErrorsAmount() as $error) {
                $this->writeLine("Class {$error['file']->getClassName()} have errors:");
                foreach ($error['errors'] as $e) {
                    foreach ($e as $item) {
                        $this->writeLine("    - " . $item);
                    }
                }
                $this->writeEmptyLine();
            }
        }
    }

    protected function writeLine(string $line): void
    {
        fwrite(STDOUT, $line . PHP_EOL);
    }

    private function writeEmptyLine(): void
    {
        $this->writeLine('');
    }

}