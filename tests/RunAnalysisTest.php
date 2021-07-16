<?php

namespace DependencyAnalysis\Tests;


use DependencyAnalysis\AnalyzerFacade;
use PHPUnit\Framework\TestCase;

class RunAnalysisTest extends TestCase
{
    public function testAnalyzerRun()
    {
        $facade = new AnalyzerFacade();
        $result = $facade->run(__DIR__ . '/simpleProject/config.php');
        $this->assertTrue($result, 'Result of analysis simpleProject should be true');
    }

}