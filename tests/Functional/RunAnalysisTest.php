<?php

namespace DependencyAnalysis\Tests\Functional;


use DependencyAnalysis\AnalyzerFacade;
use PHPUnit\Framework\TestCase;

class RunAnalysisTest extends TestCase
{
    public function testSuccessAnalyzerRun()
    {
        $facade = new AnalyzerFacade();
        $result = $facade->run(__DIR__ . '/../Data/simpleProject/config.php');
        $this->assertTrue($result->isSuccess());
        $this->assertEquals(4, $result->analyzedFilesAmount());
    }

    public function testAnalyzerRunWithErrors()
    {
        $facade = new AnalyzerFacade();
        $result = $facade->run(__DIR__ . '/../Data/brokenProject/config.php');
        $this->assertFalse($result->isSuccess());

        $this->assertEquals(4, $result->analyzedFilesAmount());
        $this->assertEquals(2, $result->countIncorrectFiles());
        $this->assertEquals(3, $result->countErrors());
    }

    public function testAnalyzerRunOnBrokenProjectSomeFiles()
    {
        $facade = new AnalyzerFacade();
        $result = $facade->run(__DIR__ . '/../Data/brokenProject/config.php', ['/Domain/ShipInterface.php']);
        $this->assertTrue($result->isSuccess());

        $this->assertEquals(1, $result->analyzedFilesAmount());
        $this->assertEquals(0, $result->countIncorrectFiles());
        $this->assertEquals(0, $result->countErrors());
    }

    public function testRunCommand()
    {
        $retval = null;
        $output = null;

        exec(__DIR__ . '/../../bin/php-da -c ' . __DIR__ . '/../Data/simpleProject/config.php', $output, $retval);

        $this->assertEquals(0, $retval);
    }
}