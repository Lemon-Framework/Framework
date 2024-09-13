<?php

declare(strict_types=1);

namespace Lemon\Http;

use Lemon\Contracts\Config\Config;
use Lemon\Contracts\Http\Session as SessionContract;
use Lemon\Http\Exceptions\SessionException;

class Session implements SessionContract
{
    private bool $started = false;

    public function __construct(
        private Config $config,
    ) {
    }

    public function __destruct()
    {
        if (!$this->started) {
            return;
        }


        try {
            // well this is terrible
            session_gc();
            session_commit();
        } catch (Exception $e) {}
    }

    /**
     * Starts session if not started.
     */
    public function init(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;

        session_start([
            'name' => $this->config->get('http.session.name'),
            'cookie_httponly' => true,
        ]);
    }

    /**
     * Sets expiration.
     */
    public function expireAt(int $seconds): static
    {
        if ($this->started) {
            session_commit();
            $this->started = false;
        }

        session_set_cookie_params($seconds);

        $this->init();

        return $this;
    }

    /**
     * Removes expiration.
     */
    public function dontExpire(): static
    {
        return $this->expireAt(0);
    }

    /**
     * Returns value of given key.
     */
    public function get(string $key): mixed
    {
        $this->init();
        if (!$this->has($key)) {
            throw new SessionException('Session key '.$key.' does not exist');
        }

        return $_SESSION[$key] ?? null;
    }

    /**
     * Sets value for given key.
     */
    public function set(string $key, mixed $value): static
    {
        $this->init();
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Determins whenever key exists.
     */
    public function has(string $key): bool
    {
        $this->init();

        return isset($_SESSION[$key]);
    }

    /**
     * Removes key.
     */
    public function remove(string $key): static
    {
        $this->init();
        unset($_SESSION[$key]);

        return $this;
    }

    /**
     * Clears session.
     */
    public function clear(): void
    {
        $this->init();
        session_unset();
    }
}
