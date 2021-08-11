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
     * @var string[]
     */
    private array $allowedExtensions;

    private bool $skipNotReadable;

    /**
     * @param string $path
     * @param string[] $allowedExtensions
     * @param bool $skipNotReadable
     */
    public function __construct(string $path, array $allowedExtensions, bool $skipNotReadable = false)
    {
        $this->assertDirectoryExists($path);

        $this->path = $path;
        $this->allowedExtensions = $allowedExtensions;
        $this->skipNotReadable = $skipNotReadable;
    }

    /**
     * @return Generator|SplFileInfo[]
     */
    public function next(): Generator
    {
        $it = new RecursiveDirectoryIterator($this->path);

        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (!in_array($file->getExtension(), $this->allowedExtensions)) {
                continue;
            }

            if (!$file->isReadable() && $this->skipNotReadable) {
                continue;
            }

            if (!$file->isReadable() && !$this->skipNotReadable) {
                throw new RuntimeException("File {$file->getRealPath()} is not readable");
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