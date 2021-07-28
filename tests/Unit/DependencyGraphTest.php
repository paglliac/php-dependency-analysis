<?php


namespace DependencyAnalysis\Tests\Unit;


use DependencyAnalysis\DependencyGraph;
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
                0
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
                0
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
                1
            ],
            'null dependency use another package' => [
                '\Domain\Tracker',
                [
                    '\Domain' => null,
                    '\Application' => ['\Domain'],
                    '\Infrastructure' => ['\Domain']
                ],
                [
                    '\Infrastructure\Domain\ClassA',
                ],
                1
            ],
            'null dependency use same package' => [
                '\Domain\Tracker',
                [
                    '\Domain' => null,
                    '\Application' => ['\Domain'],
                    '\Infrastructure' => ['\Domain']
                ],
                [
                    '\Domain\ClassA',
                ],
                0
            ]
        ];
    }

    /**
     * @dataProvider dependencyGraphDataProvider
     *
     * @param string $className
     * @param array $dependencyGraphArray
     * @param array $usesArray
     * @param int $expectedErrorsAmount
     */
    public function testEmptyDependencyGraphSuccess(string $className, array $dependencyGraphArray, array $usesArray, int $expectedErrorsAmount)
    {
        $graph = new DependencyGraph($dependencyGraphArray, false);

        $parsedClass = new ParsedClass('phpFile.php', $className, $usesArray);

        $errors = $graph->isSatisfy($parsedClass);
        $this->assertCount($expectedErrorsAmount, $errors);
    }

}