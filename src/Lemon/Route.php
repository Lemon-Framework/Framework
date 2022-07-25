<?php

declare(strict_types=1);

namespace Lemon;

use Exception;

/**
 * Lemon Router Zest
 * Provides static layer over the Lemon Router.
 *
 * @method static \Lemon\Http\Routing\Route get(string $path, $action)     Creates route with method get
 * @method static \Lemon\Http\Routing\Route post(string $path, $action)    Creates route with method post
 * @method static \Lemon\Http\Routing\Route put(string $path, $action)     Creates route with method put
 * @method static \Lemon\Http\Routing\Route head(string $path, $action)    Creates route with method head
 * @method static \Lemon\Http\Routing\Route delete(string $path, $action)  Creates route with method delete
 * @method static \Lemon\Http\Routing\Route path(string $path, $action)    Creates route with method path
 * @method static \Lemon\Http\Routing\Route options(string $path, $action) Creates route with method options
 * @method static Route any(string $path, callable $action)                The Lemon Router.
 * @method static Route template(string $path, ?string $view = null)       Creates GET route directly returning view
 * @method static Collection collection(callable $routes)                  Creates collection of routes created in given callback
 * @method static Collection file(string $file)                            Creates collection of routes created in given file
 * @method static Response dispatch(Request $request)                      Finds route depending on given request.
 * @method static Collection routes()                                      Returns all routes
 *
 * @see \Lemon\Routing\Router
 */
class Route extends Zest
{
    public static function unit(): string
    {
        return 'routing';
    }

    public static function dispatch()
    {
        // This basically prevents calling method dispatch in zest
        throw new Exception('Call to undefined method Route::dispatch');
    }
}
