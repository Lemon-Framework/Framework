<?php

declare(strict_types=1);

namespace Lemon\Debug\Handling;

use Lemon\Debug\Handling\Attributes\Doc;
use ReflectionClass;

class Consultant
{
    public array $signatures = [
        'Call to undefined function (\w+?)\(\)' => 'function',
        'Call to undefined method ([\w\\\\]+?)::(\w+?)\(\)' => 'method',
        'Undefined property: ([\w\\\\]+?)::\$(\w+?)' => 'property',
    ];

    public function giveAdvice(string $class, string $message): array
    {
        $handler = $this->findHandler($message);
        $hints = [];
        if (!empty($handler)) {
            $hints = $this->{$handler[0]}($handler[1]);
        }

        if (($section = $this->getDocSection($class))) {
            return $section;
        }

        $docs = $this->classes[$class];
        $hints[] = 'Try reading the <a href="https://lemon-framework.github.io/docs/'.$docs.'.html">documentation</a>';

        return $hints;
    }

    public function getDocSection(string $class): ?string
    {
        $ref = new ReflectionClass($class);
        return 
            $ref->getAttributes(Doc::class) 
            ? $doc->newInstance()->section 
            : null
        ;
    }

    public function findHandler(string $message): array
    {
        foreach ($this->signatures as $signature => $method) {
            if (preg_match('/^'.$signature.'$/', $message, $matches)) {
                return ['handle'.ucfirst($method), $matches];
            }
        }

        return [];
    }

    public function bestMatch(array $haystack, string $needle): ?string
    {
        $best = strlen($needle);
        $best_value = null;
        foreach ($haystack as $item) {
            if (($distance = levenshtein($item, $needle)) < $best) {
                $best = $distance;
                $best_value = $item;
            }
        }

        return $best_value;
    }

    public function handleFunction(array $matches): array
    {
        $functions = array_merge(get_defined_functions()['internal'], get_defined_functions()['user']);
        $match = $this->bestMatch($functions, $matches[1]);

        return [
            $match ? ('Did you mean '.$match.'?') : 'Function was propably not loaded. Try checking your loader',
        ];
    }

    public function handleMethod(array $matches): array
    {
        $methods = get_class_methods($matches[1]);
        $match = $this->bestMatch($methods, $matches[2]);

        return [
            $match ? ('Did you mean '.$match.'?') : '',
        ];
    }

    public function handleProperty(array $matches): array
    {
        $properties = array_keys(get_class_vars($matches[1])); // TODO difference between public and private
        $match = $this->bestMatch($properties, $matches[2]);

        return [
            $match ? ('Did you mean $'.$match.'?') : '',
        ];
    }
}
