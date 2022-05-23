<?php

declare(strict_types=1);

namespace Lemon\Templating\Juice\Compilers\Directives;

use Lemon\Templating\Exceptions\CompilerException;
use Lemon\Templating\Juice\Token;

final class SwitchDirective implements Directive
{
    public function compileOpenning(Token $token, array $stack): string
    {
        if ('' === $token->content[1]) {
            throw new CompilerException('Directive switch expects arguments', $token->line);
        }

        return '<?php switch ('.$token->content[1].'): ?>';
    }

    public function compileClosing(): string
    {
        return '<?php endswitch ?>';
    }
}
