<?php


namespace DependencyAnalysis\Tests\Unit;


use DependencyAnalysis\Config\DependencyGraph;
use DependencyAnalysis\Parser\ParsedClass;
use PHPUnit\Framework\TestCase;

class DependencyGraphTest extends TestCase
{
    public function dependencyGraphDataProvider()
    {
        return [
            'empty dependency graph' => [
                '\Application\TrackingService',
                [],
                [
                    '\SomeClass',
                    '\SomeAnotherClass'
                ],
                true
            ],
            'simple valid dependency graph' => [
                '\Application\TrackingService',
                [
                    '\Domain' => null,
                    '\Application' => ['\Domain'],
                    '\Infrastructure' => ['\Domain']
                ],
                [
                    '\Domain\ClassA',
                    '\Domain\ClassB',
                ],
                true
            ],
            'dependency include valid sub namespace' => [
                '\Application\TrackingService',
                [
                    '\Domain' => null,
                    '\Application' => ['\Domain'],
                    '\Infrastructure' => ['\Domain']
                ],
                [
                    '\Infrastructure\Domain\ClassA',
                    '\Domain\ClassB',
                ],
                false
            ],
            'null dependency' => [
                '\Domain\Tracker',
                [
                    '\Domain' => null,
                    '\Application' => ['\Domain'],
                    '\Infrastructure' => ['\Domain']
                ],
                [
                    '\Infrastructure\Domain\ClassA',
                    '\Domain\ClassB',
                ],
                false
            ]
        ];
    }

    /**
     * @dataProvider dependencyGraphDataProvider
     *
     * @param string $className
     * @param array $dependencyGraphArray
     * @param array $usesArray
     * @param bool $expectedResult
     */
    public function testEmptyDependencyGraphSuccess(string $className, array $dependencyGraphArray, array $usesArray, bool $expectedResult)
    {
        $graph = new DependencyGraph($dependencyGraphArray, false);

        $parsedClass = new ParsedClass('phpFile.php', $className, $usesArray);

        $result = $graph->isSatisfy($parsedClass);

        $this->assertEquals($expectedResult, $result);
    }

}