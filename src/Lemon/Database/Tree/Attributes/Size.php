<?php

declare(strict_types=1);

namespace Lemon\Database\Tree\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Size
{
    public function __construct(
        public readonly int $size
    ) {

    }
}
