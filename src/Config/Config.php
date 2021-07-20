<?php

namespace DependencyAnalysis\Config;

use PhpParser\ParserFactory;
use RuntimeException;

class Config
{
    private DependencyGraph $dependencyGraph;

    private string $path;

    private int $phpVersion = ParserFactory::PREFER_PHP7;

    /**
     * @var array | string[]
     */
    private array $allowedExtensions = ['php'];

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

    public function getPhpVersion(): int
    {
        return $this->phpVersion;
    }

    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    public function setPhpVersion(int $phpVersion): void
    {
        $allowed = [ParserFactory::PREFER_PHP7, ParserFactory::PREFER_PHP5, ParserFactory::ONLY_PHP7, ParserFactory::ONLY_PHP5];

        if (in_array($phpVersion, $allowed)) {
            throw new RuntimeException(sprintf("PhpVersion should have one of available values: %s. Got %s", implode(', ', $allowed), $phpVersion));
        }

        $this->phpVersion = $phpVersion;
    }

    public function setAllowedVersions(array $allowedExtensions): void
    {
        $this->allowedExtensions = $allowedExtensions;
    }
}