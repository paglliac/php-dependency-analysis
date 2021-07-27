<?php /** @noinspection ALL */


namespace DependencyAnalysis\Tests\Data\Classes;


use Domain\SomePlace;
use Exception;
use Infrastructure\ShipImplementation;

class ComplexClass
{


    public function someMethod()
    {
        $service = new Application\TrackingService();
        $service2 = new \Application\TrackingService();
        $service3 = new SomePlace\SomeClass();


        $servicesList = [new ShipImplementation()];

        $fn2 = function ($x) use ($service) {
            if ($x) {
                return new \Application\TrackingService2();
            } elseif ($service) {
                return new \Domain\Cargo();
            }

            throw new \RuntimeException();
        };

        throw new Exception();
    }


    public function anotherMethod(\Domain\ShipInterface $ship, ShipInterface $ship2)
    {
        throw ($ship) ? new \Prophecy\Exception\Doubler\ClassNotFoundException() : new \HttpRequestMethodException();
    }

}