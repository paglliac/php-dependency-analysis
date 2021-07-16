<?php

namespace DependencyAnalysis;


use DependencyAnalysis\Config\Config;
use RuntimeException;

class Analyzer
{
    public function analyze(Config $config): AnalysisResult
    {
        if($config){
            return new AnalysisResult();
        }

        throw new RuntimeException('Invalid config');
    }
}