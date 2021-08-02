<?php /** @noinspection ALL */


namespace DependencyAnalysis\Tests\Data\Classes;


use Domain\SomePlace;
use Exception;
use Infrastructure\ShipImplementation;
use PhpParser\Node\Expr\Clone_;
use PhpParser\Node\Scalar\MagicConst\Class_;

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
            } else {
                return new \Domain\Cargo();
            }

            throw new \RuntimeException();
        };

        foreach ([1, 2, 3] as $item) {
            $someValue = new Clone_();
        }

        $classA = new class() {
            public function method()
            {
                $someValue = new Class_();

                return new class() {
                    public function method()
                    {

                    }
                };
            }

        };

        try {
            switch (true) {
                case true:
                    return new \PhpParser\Node\Expr\Assign();
            }
        } catch (\Throwable $e) {
            return new \PhpParser\Node\Expr\Array_();
        }

        throw new Exception();
    }


    public function anotherMethod(\Domain\ShipInterface $ship, ShipInterface $ship2)
    {
        throw ($ship) ? new \Prophecy\Exception\Doubler\ClassNotFoundException() : new \HttpRequestMethodException();
    }

}