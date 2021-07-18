<?php


namespace DependencyAnalysis;


use Error;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RuntimeException;

class FileParser
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    public function parseFile(string $filePath): ?ParsedClass
    {
        try {
            $ast = $this->parser->parse(file_get_contents($filePath))[0];
            if (!$ast instanceof Namespace_) {
                return null;
            }

            $uses = [];
            $className = '';

            foreach ($ast->stmts as $stmt) {
                if($stmt instanceof Use_){
                    $uses[] = implode('\\', $stmt->uses[0]->name->parts);
                }
                if($stmt instanceof Class_){
                    $className = implode('\\', $ast->name->parts) . '\\' . $stmt->name->name;
                }
            }

            $parsedClass = new ParsedClass($filePath, $className, $uses);

            return  $parsedClass;
        } catch (Error $error) {
            throw new RuntimeException("Parse error: {$error->getMessage()}\n");
        }
    }
}