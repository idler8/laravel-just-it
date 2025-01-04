<?php

namespace Justit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use ReflectionClass;

class ApiDocument
{
    public static function parse(string $describe, bool $parameter = false): ?array
    {
        $comments = preg_replace('#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]?(.*)?#u', '$1', $describe);
        $comments = preg_replace('#\*+\/#u', '', $comments);
        $comments = str_replace(["\r\n", "\r"], "\n", $comments);
        $comments = preg_replace("#\n+#", "\n", $comments);
        $comments = collect(explode("\n",  $comments))->filter()->values();
        $comments = $comments->groupBy(function ($comment) {
            if (Str::startsWith($comment, '@')) {
                if (Str::startsWith($comment, '@param')) {
                    return 'parameters';
                };
                return 'ignore';
            };
            return 'describe';
        });
        $describe = collect($comments->get('describe', []));
        if (empty($name = $describe->shift())) return null;
        $response = ['name' => trim($name)];
        if ($describe->isNotEmpty()) {
            $response['describe'] = $describe->implode('\n');
        }
        if ($parameter) {
            $parameters = collect($comments->get('parameters', []));
            $response['parameters'] = $parameters->map(function ($parameter) {
                $param = collect(explode(' ', $parameter))->filter()->values();
                if ($param->count() === 3) {
                    return ['key' => $param->get(1), 'name' => $param->get(2)];
                } elseif ($param->count() === 4) {
                    return ['key' => $param->get(2), 'name' => $param->get(3)];
                }
                return null;
            })->filter()->values();
        }

        return $response;
    }
    /** 
     * 获取接口结构 
     * */
    public static function document(string $middleware): Collection
    {
        return collect(Route::getRoutes())->filter(function ($route) use ($middleware) {
            return collect($route->middleware())->contains(function ($value) use ($middleware) {
                return Str::startsWith($value, $middleware);
            });
        })->map(function ($original) {
            return collect($original->methods)->map(function ($method) use ($original) {
                $controller = $original->getAction('controller');
                if (!is_string($controller)) return null;
                if (!Str::contains($controller, '@')) return null;
                return [
                    'uri' => $original->uri,
                    'method' => $method,
                    'controller' => $controller,
                ];
            })->filter();
        })->flatten(1)->groupBy('controller', true)->map(function ($urls, $name) {
            list($controller, $action) = explode('@', $name);
            $class = new ReflectionClass($controller);
            if (empty($describe = $class->getMethod($action)->getDocComment())) {
                return null;
            }
            if (empty($response = static::parse($describe, true))) {
                return null;
            }
            $response['key'] = $name;
            $response['urls'] = collect($urls)->map(function ($item) {
                return $item['method'] . ':' . $item['uri'];
            })->values();
            return $response;
        })->filter()->values();
    }
}
