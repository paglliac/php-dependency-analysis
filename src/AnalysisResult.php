<?php


namespace DependencyAnalysis;


class AnalysisResult
{
    private $success = true;

    public function isSuccess(): bool
    {
        return $this->success;
    }
}