<?php

declare(strict_types=1);

namespace Lemon\Config;

use Lemon\Config\Exceptions\ConfigException;
use Lemon\Kernel\Lifecycle;
use Lemon\Support\Types\Arr;
use Lemon\Support\Types\Str;

class Part
{
    public function __construct(
        private Lifecycle $lifecycle,
        private array $data,
        private string $name
    ) {
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function has(string $key): bool
    {
        return Arr::hasKey($this->data, $key);
    }

    public function get(string $key)
    {
        $keys = Str::split($key, '.');
        if (!$this->has($keys[0])) {
            throw new ConfigException('Key '.$key.' of part '.$this->name.' does not exist');
        }
        $result = $this->data[$keys[0]];
        foreach ($keys['1..'] as $key) {
            if (!Arr::hasKey($result, $key)) {
                throw new ConfigException('Key '.$key.' of part '.$this->name.' does not exist');
            }
            $result = $result[$key];
        }

        return $result;
    }

    public function set(string $key, mixed $value): static
    {
        if (!$this->has($key)) {
            throw new ConfigException('Config key '.$key.' does not exist');
        }

        $this->data[$key] = $value;

        return $this;
    }

    public function file(string $key, string $extension = null): string
    {
        return $this->lifecycle->file($this->get($key), $extension);
    }

    public function data(): array
    {
        return $this->data;
    }
}