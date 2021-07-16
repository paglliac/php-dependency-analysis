<?php


namespace DependencyAnalysis;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class FileIterator
{
    /**
     * @var string
     */
    private $path;

    public function __construct(string $path)
    {
        $this->assertDirectoryExists($path);

        $this->path = $path;
    }

    /**
     * @return \Generator|SplFileInfo[]
     */
    public function next()
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