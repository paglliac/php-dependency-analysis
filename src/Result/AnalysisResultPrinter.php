<?php

namespace DependencyAnalysis\Result;

interface AnalysisResultPrinter
{
    public function print(AnalysisResult $analysisResult): void;
}