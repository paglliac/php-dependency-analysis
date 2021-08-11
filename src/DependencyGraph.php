<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Parser\ParsedClass;
use ReflectionClass;
use Throwable;

class DependencyGraph
{
    private array $dependencies;
    private bool $skipNonPresentedNameSpace;
    private bool $skipVendorDir;
    private string $vendorDir;
    private array $validDependencies;

    public function __construct(array $dependencies, bool $failOnNonPresentedNameSpace, bool $skipVendorDir, string $vendorDir, array $validForAll = [])
    {
        $this->dependencies = $dependencies;
        $this->skipNonPresentedNameSpace = !$failOnNonPresentedNameSpace;
        $this->skipVendorDir = $skipVendorDir;
        $this->vendorDir = $vendorDir;
        $this->validDependencies = $validForAll;
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
        foreach (array_merge($validDependencies, $this->validDependencies) as $item) {
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

        } catch (Throwable $e) {
            return false;
        }
    }
}