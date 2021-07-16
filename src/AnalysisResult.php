<?php


namespace DependencyAnalysis;


class AnalysisResult
{
    private $analyzedFilesAmount;

    private $success = true;

    public function addAnalyzedFile(): void
    {
        $this->analyzedFilesAmount++;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function analyzedFilesAmount(): int
    {
        return $this->analyzedFilesAmount;
    }
}