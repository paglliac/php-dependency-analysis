<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Parser\ParsedClass;
use ReflectionClass;
use ReflectionException;

class DependencyGraph
{
    private array $dependencies;
    private bool $skipNonPresentedNameSpace;
    private bool $skipVendorDir;
    private string $vendorDir;

    public function __construct(array $dependencies, bool $failOnNonPresentedNameSpace, bool $skipVendorDir, string $vendorDir = '')
    {
        $this->dependencies = $dependencies;
        $this->skipNonPresentedNameSpace = !$failOnNonPresentedNameSpace;
        $this->skipVendorDir = $skipVendorDir;
        $this->vendorDir = $vendorDir;
    }

    public function toArray(): array
    {
        return $this->dependencies;
    }

    public function isSatisfy(ParsedClass $parsedClass): array
    {
        if (empty($this->dependencies)) {
            return [];
        }

        if (!$parsedClass->haveUses()) {
            return [];
        }

        $validDependencies = $this->findPackageDependencies($parsedClass->getClassName());

        if (is_null($validDependencies)) {
            if ($this->skipNonPresentedNameSpace) {
                return [];
            }

            return ["Dependencies not presented for {$parsedClass->getClassName()} in dependency graph"];
        }


        $errors = [];

        foreach ($parsedClass->getUses() as $use) {
            if (!$this->isUseSatisfyValidDependencies($use, $validDependencies)) {
                $errors[] = ["Class {$parsedClass->getClassName()} using class {$use} which not satisfy dependency graph"];
            }
        }

        return $errors;
    }

    public function findPackageDependencies(string $className): ?array
    {
        foreach ($this->dependencies as $namespace => $dep) {
            if (strpos($className, $namespace) === 0) {
                return is_null($dep) ? [$namespace] : array_merge([$namespace], $dep);
            }
        }

        return null;
    }

    private function isUseSatisfyValidDependencies(string $use, array $validDependencies): bool
    {
        foreach ($validDependencies as $item) {
            if (strpos($use, $item) === 0) {
                return true;
            }
        }

        if (!$this->skipVendorDir) {
            return false;
        }

        return $this->isVendorClass($use);
    }

    private function isVendorClass(string $use): bool
    {
        try {
            $refClass = new ReflectionClass($use);

            if (strpos($refClass->getFileName(), $this->vendorDir) !== false) {
                return true;
            }
           
            return false;

        } catch (ReflectionException $e) {
            return false;
            // TODO need to define not only class but and namespaces
        }
    }
}