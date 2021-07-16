<?php


namespace DependencyAnalysis;


class AnalyzeResult
{
    private $success = true;

    public function isSuccess(): bool
    {
        return $this->success;
    }
}