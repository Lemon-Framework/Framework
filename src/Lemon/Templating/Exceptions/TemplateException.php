<?php

declare(strict_types=1);

namespace Lemon\Templating\Exceptions;
use Lemon\Debug\Handling\Attributes\Doc;

#[Doc('getting_started/templates')]
class TemplateException extends \ErrorException
{
    public static function from(\Throwable $original, string $source): self
    {
        return new self($original->getMessage(), $original->getCode(), 1, $source, $original->getLine());
    }
}
