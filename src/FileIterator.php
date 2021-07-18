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

    public function __construct(string $path)
    {
        $this->assertDirectoryExists($path);

        $this->path = $path;
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

            if($file->getExtension() !== 'php'){
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