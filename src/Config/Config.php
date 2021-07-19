<?php

namespace DependencyAnalysis\Config;

use RuntimeException;

class Config
{
    private DependencyGraph $dependencyGraph;

    private string $path;

    public function __construct(string $path, DependencyGraph $dependencyGraph)
    {
        $this->dependencyGraph = $dependencyGraph;

        $this->assertDirectoryExists($path);
        $this->path = $path;
    }

    public function getDependencyGraph(): DependencyGraph
    {
        return $this->dependencyGraph;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function assertDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            throw new RuntimeException("Source directory {$path} does not exists");
        }
    }

}