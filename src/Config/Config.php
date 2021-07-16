<?php

namespace DependencyAnalysis\Config;

use RuntimeException;
use function PHPUnit\Framework\directoryExists;

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

        $this->assertDirectoryExists($path);
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