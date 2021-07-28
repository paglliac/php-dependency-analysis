<?php


namespace DependencyAnalysis;


use DependencyAnalysis\Parser\ParsedClass;

class DependencyGraph
{
    private array $dependencies;
    private bool $skipNonPresentedNameSpace;

    public function __construct(array $dependencies, bool $failOnNonPresentedNameSpace)
    {
        $this->dependencies = $dependencies;
        $this->skipNonPresentedNameSpace = !$failOnNonPresentedNameSpace;
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
            } else {
                return ["Dependencies not presented for {$parsedClass->getClassName()} in dependency graph"];
            }
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

        return false;
    }
}