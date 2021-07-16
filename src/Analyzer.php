<?php

namespace DependencyAnalysis;


class Analyzer
{
    public function analyze(Config $config): AnalyzeResult
    {
        return new AnalyzeResult();
    }
}