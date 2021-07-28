<?php


namespace DependencyAnalysis\Result;


use DependencyAnalysis\Parser\ParsedClass;

class AnalysisResult
{
    private array $correctFiles = [];

    private array $incorrectFiles = [];

    private int $errorsAmount = 0;

    public function addCorrectFile(ParsedClass $parsedClass): void
    {
        $this->correctFiles[] = $parsedClass;
    }

    public function isSuccess(): bool
    {
        return count($this->incorrectFiles) === 0;
    }

    public function analyzedFilesAmount(): int
    {
        return count($this->correctFiles) + count($this->incorrectFiles);
    }

    public function addIncorrectFile(ParsedClass $parsedClass, array $errors): void
    {
        $this->incorrectFiles[] = [
            'file' => $parsedClass,
            'errors' => $errors
        ];

        $this->errorsAmount += count($errors);
    }

    public function getErrorsAmount(): array
    {
        return $this->incorrectFiles;
    }

    public function countIncorrectFiles(): int
    {
        return count($this->incorrectFiles);
    }

    public function countErrors(): int
    {
        return $this->errorsAmount;
    }
}