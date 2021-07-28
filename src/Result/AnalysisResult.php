<?php


namespace DependencyAnalysis\Result;


use DependencyAnalysis\Parser\ParsedClass;

class AnalysisResult
{
    private array $correctFiles;

    private array $incorrectFiles;

    private bool $success = true;

    public function addCorrectFile(ParsedClass $parsedClass): void
    {
        $this->correctFiles[] = $parsedClass;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function analyzedFilesAmount(): int
    {
        return count($this->correctFiles);
    }

    public function addIncorrectFile(ParsedClass $parsedClass, array $errors): void
    {
        $this->incorrectFiles[] = [
            'file' => $parsedClass,
            'errors' => $errors
        ];

        $this->success = false;
    }
}