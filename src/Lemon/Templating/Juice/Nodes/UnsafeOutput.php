<?php

declare(strict_types=1);

namespace Lemon\Templating\Juice\Nodes;

use Lemon\Contracts\Templating\Juice\Expression;
use Lemon\Contracts\Templating\Juice\Node;
use Lemon\Templating\Juice\Position;

class UnsafeOutput implements Node
{
    public function __construct(
        public readonly Expression $expression,
        public readonly Position $position,
    ) { 
    
    }
}
