<?php

namespace DependencyAnalysis\Tests\Functional;


use DependencyAnalysis\AnalyzerFacade;
use PHPUnit\Framework\TestCase;

class RunAnalysisTest extends TestCase
{
    public function testAnalyzerRun()
    {
        $facade = new AnalyzerFacade();
        $result = $facade->run(__DIR__ . '/../Data/simpleProject/config.php');
        $this->assertTrue($result->isSuccess(), 'Result of analysis simpleProject should be true');
        $this->assertEquals(4, $result->analyzedFilesAmount());
    }

}