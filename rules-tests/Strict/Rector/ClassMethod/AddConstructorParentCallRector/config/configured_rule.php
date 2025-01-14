<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Strict\Rector\ClassMethod\AddConstructorParentCallRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AddConstructorParentCallRector::class);
};
