<?php

namespace Lemon\Http\Routing;

/**
 * Class representing route group
 *
 * @param Array $parameters
 * @param Array $routes
 */
class RouteGroup
{
    /**
     * Group name
     */
    public $name;

    /**
     * Group route prefix
     */
    public $prefix;

    /**
     * Group route middlewares
     */
    public $middlewares;

    /**
     * Group member routes
     */
    public $routes;


    public function __construct(Array $parameters, Array $routes)
    {
        $this->name = $parameters["name"] ?? "";
        $this->middlewares = $parameters["middlewares"] ?? [];
        $this->prefix = $parameters["prefix"] ?? "/";
        $this->routes = $routes;
        $this->resolve();
        $this->update();
    }

    /**
     * Resolves nested route groups and arrays of routes
     */ 
    public function resolve()
    {
        foreach ($this->routes as $pos => $route)
        {
            if (is_array($route))
                $this->resolveRoute($pos, $route);

            else if ($route instanceof RouteGroup)
                $this->resolveRoute($pos, $route->routes);
        }
    }

    /**
     * Resolves routes that aren't Route instance
     */
    public function resolveRoute($pos, $routes)
    {
        unset($this->routes[$pos]);
        $this->routes = array_merge($this->routes, $routes);
    }

    /**
     * Updates every group member to given parameters
     */
    public function update()
    {
        foreach ($this->routes as $route)
        {
            if ($this->name != "")
                $route->name = $this->name . ":" . $route->name;
            $route->middleware($this->middlewares);
            $route->prefix($this->prefix);
        }
    }


}
 
