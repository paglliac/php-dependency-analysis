<?php


namespace DependencyAnalysis\Parser;


use Error;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Throw_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RuntimeException;

class FileParser
{
    private Parser $parser;

    private ParsingProcess $process;

    public function __construct(int $phpVersion)
    {
        $this->parser = (new ParserFactory)->create($phpVersion);
    }

    public function parseFile(string $filePath): ?ParsedClass
    {
        try {
            $this->process = new ParsingProcess();

            $ast = $this->parser->parse(file_get_contents($filePath))[0];

            if (!$ast instanceof Namespace_) {
                return null;
            }

            $this->process->namespaceParts = $ast->name->parts;

            $this->processStmts($ast->stmts);

            return new ParsedClass($filePath, $this->process->className, array_unique($this->process->uses));
        } catch (Error $error) {
            throw new RuntimeException("Parse error: {$error->getMessage()}\n");
        }
    }

    public function processStmts(?array $stmts): void
    {
        if (!$stmts) {
            return;
        }

        foreach ($stmts as $stmt) {
            if ($stmt instanceof Class_) {
                $this->process->className = '\\' . implode('\\', $this->process->namespaceParts) . '\\' . $stmt->name->name;
            } elseif ($stmt instanceof Stmt\If_) {
                $this->processStmts($stmt->stmts ?? []);
                $this->processStmts($stmt->elseifs ?? []);
                $this->processStmts($stmt->else->stmts ?? []);
            } elseif ($stmt instanceof Stmt\Return_) {
                $this->processExpression($stmt->expr);
            } elseif ($stmt instanceof Use_) {
                $this->pushToUses($stmt->uses[0]->name->parts);
                $this->pushToImports($stmt->uses[0]->name->parts);
            } elseif ($stmt instanceof New_) {
                $this->processClass($stmt->class);
            } elseif ($stmt instanceof ClassMethod) {
                foreach ($stmt->params as $param) {
                    if ($param->type instanceof FullyQualified) {
                        $this->pushToUses($param->type->parts);
                    } elseif ($param->type instanceof Name) {
                        $this->pushToUses($param->type->parts, true);
                    }
                }
                $this->processStmts($stmt->stmts);
            } elseif ($stmt instanceof Throw_) {
                $this->processExpression($stmt->expr);
            } elseif ($stmt instanceof Stmt\Expression) {
                $this->processExpression($stmt->expr);
            }

            if (isset($stmt->stmts)) {
                $this->processStmts($stmt->stmts);
            }
        }
    }


    private function processExpression(Expr $expr)
    {
        if ($expr instanceof New_) {
            $this->processNew_($expr);
        }

        if ($expr instanceof Expr\Ternary) {
            $this->processTernary($expr);
        }

        if ($expr instanceof Expr\Assign) {
            if ($expr->expr instanceof Expr) {
                $this->processExpression($expr->expr);
            }
        }

        if ($expr instanceof Expr\Closure) {
            $this->processStmts($expr->stmts);
        }

    }

    private function processNew_(New_ $expr): void
    {
        $this->processClass($expr->class);
    }

    private function processClass(Name $class): void
    {
        if ($class instanceof FullyQualified) {
            $this->pushToUses($class->parts);
        } elseif ($class instanceof Name) {
            $this->pushToUses($class->parts, true);
        }
    }

    private function processTernary(Expr\Ternary $ternary)
    {
        if ($ternary->if instanceof Expr) {
            $this->processExpression($ternary->if);
        }

        if ($ternary->else instanceof Expr) {
            $this->processExpression($ternary->else);
        }

        if ($ternary->cond instanceof Expr) {
            $this->processExpression($ternary->cond);
        }
    }

    private function pushToImports(array $parts)
    {
        $this->process->imports[] = '\\' . implode('\\', $parts);
    }

    private function pushToUses(array $parts, bool $checkUses = false)
    {
        $partsString = implode('\\', $parts);

        if (!$checkUses) {
            $this->process->uses[] = '\\' . $partsString;

            return;
        }

        foreach ($this->process->imports as $use) {
            if (count($parts) === 1 && substr($use, -strlen($partsString), strlen($partsString)) === $partsString) {
                return;
            } elseif (substr($use, -strlen($parts[0]), strlen($parts[0])) === $parts[0]) {
                $this->process->uses[] = $use . '\\' . implode('\\', array_slice($parts, 1));
                return;
            }
        }


        $this->process->uses[] = '\\' . implode('\\', array_merge($this->process->namespaceParts, $parts));
    }
}