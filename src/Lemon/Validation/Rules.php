<?php

declare(strict_types=1);

namespace Lemon\Validation;

use Lemon\Support\Types\Arr;

class Rules
{
    private array $rules = [];

    public function numeric(string $target)
    {
        return is_numeric($target);
    }

    public function notNumeric(string $target)
    {
        return !$this->numeric($target);
    }

    public function email(string $target)
    {
        return filter_var($target, FILTER_VALIDATE_EMAIL) === $target;
    }

    public function url(string $target)
    {
        return filter_var($target, FILTER_VALIDATE_URL) === $target;
    }

    public function color(string $target)
    {
        return preg_match('/#([a-f0-9]{2}){3}/', $target);
    }

    public function max(string $target, int $max)
    {
        return strlen($target) <= $max;
    }

    public function min(string $target, int $min)
    {
        return strlen($target) >= $min;
    }

    public function regex(string $target, string $patern): bool
    {
        return preg_match($patern, $target) > 0;
    }

    public function notRegex(string $target, string $patern): bool
    {
        return !$this->regex($target, $patern);
    }

    public function file()
    {
        // todo
    }

    public function rule(string $name, callable $action): static
    {
        $this->rules[$name] = $action;

        return $this;
    }

    public function call(string $key, array $rule): bool
    {
        $args = [];
        if (1 === count($rule)) {
            $args = $rule[1];
        }

        if (method_exists($this, $rule[0])) {
            return $this->{$rule[0]}($key, ...$args);
        }

        if (Arr::hasKey($this->rules, $rule[0])) {
            return $this->rules[$rule[0]]($key, ...$args);
        }

        throw new ValidatorException('Validator rule '.$rule[0].' does not exist');
    }
}