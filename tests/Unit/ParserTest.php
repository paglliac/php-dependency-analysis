<?php


namespace DependencyAnalysis\Tests\Unit;


use DependencyAnalysis\Parser\FileParser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

class ParserTest extends TestCase
{
    public function testSimpleClassParse()
    {
        $parser = new FileParser(ParserFactory::PREFER_PHP7);
        $parsedClass = $parser->parseFile(new SplFileInfo(__DIR__ . '/../Data/Classes/SimpleClass.php'));

        $this->assertEquals('\DependencyAnalysis\Tests\Data\Classes\SimpleClass', $parsedClass->getClassName());
        $this->assertCount(1, $parsedClass->getUses());
        $this->assertEquals('\Domain\Cargo', $parsedClass->getUses()[0]);
    }


    public function testComplexClassParse()
    {
        $parser = new FileParser(ParserFactory::PREFER_PHP7);
        $parsedClass = $parser->parseFile(new SplFileInfo(__DIR__ . '/../Data/Classes/ComplexClass.php'));


        $this->assertEquals('\DependencyAnalysis\Tests\Data\Classes\ComplexClass', $parsedClass->getClassName());
        $this->assertCount(18, $parsedClass->getUses());
        $this->assertNotContains('\\Domain\\SomePlace', $parsedClass->getUses());
    }

}