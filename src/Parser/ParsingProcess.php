<?php


namespace DependencyAnalysis\Parser;


class ParsingProcess
{
    /**
     * @var string[]
     */
    public array $uses = [];

    /**
     * @var string[]
     */
    public array $imports = [];

    public string $className = '';

    /**
     * @var string[]
     */
    public array $namespaceParts = [];
}