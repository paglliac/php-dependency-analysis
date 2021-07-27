<?php


namespace DependencyAnalysis\Parser;


class ParsedClass
{
    private string $fileName;

    private string $className;

    private array $uses;

    public function __construct(string $fileName, string $className, array $uses)
    {
        $this->fileName = $fileName;
        $this->className = $className;
        $this->uses = $uses;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function haveUses(): bool
    {
        return !empty($this->uses);
    }

    public function getUses(): array
    {
        return $this->uses;
    }
}