<?php

declare(strict_types=1);

namespace Rector\Core\PhpParser\NodeFinder;

use PhpParser\Node\Const_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;

final class LocalConstantFinder
{
    public function __construct(
        private readonly NodeTypeResolver $nodeTypeResolver,
        private readonly NodeNameResolver $nodeNameResolver,
        private readonly BetterNodeFinder $betterNodeFinder,
    ) {
    }

    public function match(ClassConstFetch $classConstFetch): ?Const_
    {
        $class = $this->betterNodeFinder->findParentType($classConstFetch, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }

        $constantName = $this->nodeNameResolver->getName($classConstFetch->name);
        if ($constantName === null) {
            return null;
        }

        $constantClassType = $this->nodeTypeResolver->getType($classConstFetch->class);
        if (! $constantClassType instanceof TypeWithClassName) {
            return null;
        }

        if (! $this->nodeNameResolver->isName($class, $constantClassType->getClassName())) {
            return null;
        }

        return $this->findConstantByName($class, $constantName);
    }

    private function findConstantByName(Class_ $class, string $constantName): ?Const_
    {
        foreach ($class->getConstants() as $classConsts) {
            foreach ($classConsts->consts as $const) {
                if (! $this->nodeNameResolver->isName($const->name, $constantName)) {
                    continue;
                }

                return $const;
            }
        }

        return null;
    }
}
