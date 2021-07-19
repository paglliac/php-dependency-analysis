<?php


namespace DependencyAnalysis\Config;


use DependencyAnalysis\ParsedClass;

class DependencyGraph
{
    private array $dependencies;

    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function toArray(): array
    {
        return $this->dependencies;
    }

    public function isSatisfy(ParsedClass $parsedClass): bool
    {
        if (!$parsedClass->haveUses()) {
            return true;
        }

        foreach ($this->dependencies as $namespace => $dependency) {
            if (strpos($parsedClass->getClassName(), $namespace) !== false) {
                foreach ($parsedClass->getUses() as $use) {
                    foreach ($dependency as $item) {
                        if (strpos($use, $item) !== false) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}