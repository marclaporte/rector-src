<?php

declare(strict_types=1);

namespace Rector\TypeDeclaration\Matcher;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use Rector\Core\NodeAnalyzer\PropertyFetchAnalyzer;

final class PropertyAssignMatcher
{
    public function __construct(
        private readonly PropertyFetchAnalyzer $propertyFetchAnalyzer
    ) {
    }

    /**
     * Covers:
     * - $this->propertyName = $expr;
     * - $this->propertyName[] = $expr;
     */
    public function matchPropertyAssignExpr(Assign $assign, string $propertyName): ?Expr
    {
        $assignVar = $assign->var;
        if ($this->propertyFetchAnalyzer->isLocalPropertyFetchName($assignVar, $propertyName)) {
            return $assign->expr;
        }

        if (! $assignVar instanceof ArrayDimFetch) {
            return null;
        }

        if ($this->propertyFetchAnalyzer->isLocalPropertyFetchName($assignVar->var, $propertyName)) {
            return $assign->expr;
        }

        return null;
    }
}
