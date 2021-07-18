<?php


namespace DependencyAnalysis;


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

}