<?php


namespace DependencyAnalysis\Parser;


use Error;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Continue_;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Finally_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Global_;
use PhpParser\Node\Stmt\Goto_;
use PhpParser\Node\Stmt\HaltCompiler;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Label;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\Throw_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\While_;
use PhpParser\Node\UnionType;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use RuntimeException;


//use \PhpParser\Node\Stmt\Enum_;
//use \PhpParser\Node\Stmt\EnumCase;
//use \PhpParser\Node\Stmt\GroupUse;
//
//use \PhpParser\Node\Stmt\Property;
//use \PhpParser\Node\Stmt\PropertyProperty;
//use \PhpParser\Node\Stmt\Static_;
//use \PhpParser\Node\Stmt\StaticVar;
//use \PhpParser\Node\Stmt\Trait_;
//use \PhpParser\Node\Stmt\TraitUse;
//use \PhpParser\Node\Stmt\UseUse;

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
            throw new RuntimeException("Parse error: {$error->getMessage()}\n", $error->getCode(), $error);
        }
    }

    public function processStmts(?array $stmts): void
    {
        if (!$stmts) {
            return;
        }

        foreach ($stmts as $stmt) {
            if ($stmt instanceof Class_) {
                if (!$this->process->className) {
                    $this->process->className = '\\' . implode('\\', $this->process->namespaceParts) . '\\' . $stmt->name->name;
                }
                $this->processStmts($stmt->stmts);
                $this->processName($stmt->extends);
                $this->processNamesList(...$stmt->implements);
            } elseif ($stmt instanceof If_) {
                $this->processExpression($stmt->cond);

                $this->processStmts($stmt->stmts ?? []);
                $this->processStmts($stmt->elseifs ?? []);
                $this->processStmts($stmt->else->stmts ?? []);
            } elseif ($stmt instanceof Return_) {
                $this->processExpression($stmt->expr);
            } elseif ($stmt instanceof Use_) {
                $this->pushToUses($stmt->uses[0]->name->parts);
                $this->pushToImports($stmt->uses[0]->name->parts);
            } elseif ($stmt instanceof New_) {
                if ($stmt->class instanceof Class_) {
                    $this->processClass($stmt->class);
                }

                if ($stmt->class instanceof Expr) {
                    $this->processExpression($stmt->class);
                }

                $this->processArgs($stmt->args);
            } else if ($stmt instanceof TryCatch) {
                $this->processStmts($stmt->stmts);
                $this->processStmts($stmt->catches);
                $this->processStmts([$stmt->finally]);
            } elseif ($stmt instanceof Catch_) {
                $this->processNamesList(...$stmt->types);
                $this->processStmts($stmt->stmts);
            } elseif ($stmt instanceof Switch_) {
                $this->processExpression($stmt->cond);
                $this->processStmts($stmt->cases);
            } elseif ($stmt instanceof Case_) {
                $this->processExpression($stmt->cond);
                $this->processStmts($stmt->stmts);
            } elseif ($stmt instanceof ClassMethod) {
                foreach ($stmt->params as $param) {
                    $this->processName($param->type);
                }
                $this->processVariableType($stmt->returnType);
                $this->processStmts($stmt->stmts);
            } elseif ($stmt instanceof Throw_) {
                $this->processExpression($stmt->expr);
            } elseif ($stmt instanceof Expression) {
                $this->processExpression($stmt->expr);
            } elseif (
                $stmt instanceof Break_
                || $stmt instanceof Continue_
                || $stmt instanceof Declare_
                || $stmt instanceof DeclareDeclare
                || $stmt instanceof Global_
                || $stmt instanceof Goto_
                || $stmt instanceof HaltCompiler
                || $stmt instanceof InlineHTML
                || $stmt instanceof Label
                || $stmt instanceof Nop
                || $stmt instanceof Unset_
            ) {
                continue;
            } elseif ($stmt instanceof Const_) {
                $this->processConsts($stmt->consts);
            } elseif ($stmt instanceof ClassConst) {
                $this->processConsts($stmt->consts);
            } elseif ($stmt instanceof Echo_) {
                $this->processExpressionsList(...$stmt->exprs);
            } elseif ($stmt instanceof Do_) {
                $this->processConsts($stmt->stmts);
            } elseif ($stmt instanceof Finally_) {
                $this->processConsts($stmt->stmts);
            } elseif ($stmt instanceof For_) {
                $this->processExpressionsList(...$stmt->cond);
                $this->processExpressionsList(...$stmt->init);
                $this->processExpressionsList(...$stmt->loop);

                $this->processConsts($stmt->stmts);
            } elseif ($stmt instanceof Foreach_) {
                $this->processExpression($stmt->expr);

                $this->processConsts($stmt->stmts);
            } elseif ($stmt instanceof Function_) {
                $this->processParamsList(...$stmt->params);
                $this->processVariableType($stmt->returnType);
                $this->processStmts($stmt->stmts);
            } elseif ($stmt instanceof While_) {
                $this->processExpression($stmt->cond);
                $this->processStmts($stmt->stmts);
            } elseif ($stmt instanceof Interface_) {
                $this->processStmts($stmt->stmts);
                $this->processNamesList(...$stmt->extends);
            }

            if (isset($stmt->stmts)) {
                $this->processStmts($stmt->stmts);
            }
        }
    }

    private function processExpressionsList(Expr ...$exprs)
    {
        foreach ($exprs as $expr) {
            $this->processExpression($expr);
        }
    }

    private function processExpression(?Expr $expr): void
    {
        if (!$expr) {
            return;
        }

        if ($expr instanceof New_) {
            $this->processNew_($expr);
        }

        if ($expr instanceof Ternary) {
            $this->processTernary($expr);
        }

        if ($expr instanceof Assign) {
            if ($expr->expr instanceof Expr) {
                $this->processExpression($expr->expr);
            }
        }


        if ($expr instanceof Closure) {
            $this->processStmts($expr->stmts);
        }

    }

    private function processNew_(New_ $expr): void
    {
        $this->processClass($expr->class);
    }

    private function processClass($class): void
    {
        if ($class instanceof Name) {
            $this->processName($class);
        } elseif ($class instanceof Class_) {
            $this->processStmts($class->stmts);
        }
    }

    public function processNamesList(Name ...$names): void
    {
        foreach ($names as $name) {
            $this->processName($name);
        }
    }

    private function processName(Name $name): void
    {
        if ($name instanceof FullyQualified) {
            $this->pushToUses($name->parts);
        } elseif ($name instanceof Name) {
            $this->pushToUses($name->parts, true);

        }
    }

    private function processTernary(Ternary $ternary)
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

    private function processParamsList(Param...$params)
    {
        foreach ($params as $param) {
            $this->processParam($param);
        }
    }

    private function processParam(Param $param)
    {
        $variableType = $param->type;

        $this->processVariableType($variableType);
    }

    private function processArgs(array $args): void
    {
        foreach ($args as $arg) {
            $this->processExpression($arg->value);
        }
    }

    private function processConsts(array $consts): void
    {
        foreach ($consts as $const) {
            if ($const instanceof Expr) {
                $this->processExpression($const->expr);
            }
        }
    }

    /**
     * @param Name|null $variableType
     */
    private function processVariableType(?Name $variableType): void
    {
        if ($variableType instanceof NullableType) {
            if ($variableType->type instanceof Name) {
                $this->processName($variableType->type);
            }
        }
        if ($variableType instanceof UnionType) {
            $this->processNamesList(...$variableType->types);
        }

        if ($variableType instanceof Name) {
            $this->processName($variableType);
        }
    }
}