<?php


namespace DependencyAnalysis\Config;


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

    public function isSatisfy(ParsedClass $parsedClass): bool
    {
        if (empty($this->dependencies)) {
            return true;
        }

        if (!$parsedClass->haveUses()) {
            return true;
        }

        $validDependencies = $this->findPackageDependencies($parsedClass->getClassName());

        if (is_null($validDependencies)) {
            return $this->skipNonPresentedNameSpace;
        }


        foreach ($parsedClass->getUses() as $use) {
            if (!$this->isUseSatisfyValidDependencies($use, $validDependencies)) {
                return false;
            }
        }

        return true;
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