<?php

namespace DependencyAnalysis\Config;

class Config
{
    /**
     * @var array
     */
    private $dependencyGraph;

    /**
     * @var string
     */
    private $path;

    public function __construct(string $path, array $dependencyGraph)
    {
        $this->dependencyGraph = $dependencyGraph;
        $this->path = $path;
    }

    public function getDependencyGraph(): array
    {
        return $this->dependencyGraph;
    }

    public function getPath(): string
    {
        return $this->path;
    }

}