<?php

declare(strict_types=1);

namespace Lemon\Templating\Juice\Nodes\Expression;

use Lemon\Contracts\Templating\Juice\Expression;
use Lemon\Templating\Juice\Position;

class AnonymousFunction implements Expression
{
    public function __construct(
        /**
         * @param array<Expression> $params
         */
        public readonly array $params,
        public readonly Expression $expression,
        public readonly Position $position,
    ) {

    }
}
