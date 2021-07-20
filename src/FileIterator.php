<?php


namespace DependencyAnalysis;


use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class FileIterator
{
    private string $path;

    /**
     * @var array|string[]
     */
    private array $allowedExtensions;

    /**
     * @param string $path
     * @param array|string[] $allowedExtensions
     */
    public function __construct(string $path, array $allowedExtensions)
    {
        $this->assertDirectoryExists($path);

        $this->path = $path;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @return Generator|SplFileInfo[]
     */
    public function next(): Generator
    {
        $it = new RecursiveDirectoryIterator($this->path);

        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if($file->isDir()){
                continue;
            }

            if (!in_array($file->getExtension(), $this->allowedExtensions)) {
                continue;
            }

            yield $file;
        }

        return;
    }

    public function assertDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            throw new RuntimeException("Source directory {$path} does not exists");
        }
    }
}