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

    public function testRunCommand()
    {
        $retval = null;
        $output = null;

        exec(__DIR__ . '/../../bin/php-da -c ' . __DIR__ . '/../Data/simpleProject/config.php', $output, $retval);

        $this->assertEquals(0, $retval);
    }
}