<?php

namespace Justit;

use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Reflector;
use Reflection;
use ReflectionClass;
use ReflectionMethod;

/**
 * 资源操作逻辑
 */
class Resource
{
    public const keyUsedFilter = ':';
    public const keyUsedSelect = '-';
    public const keyUsedRelation = '+';
    public const keyUsedRange = '@';
    /** 命名空间转化为资源名 */
    public static function name(string $namespace)
    {
        return str_replace(['\\'], ['.'], $namespace);
    }
    /** 资源名转化为命名空间 */
    public static function namespace(string $name)
    {
        return str_replace(['_', '.', '/'], ['', '\\', '\\'], ucwords($name, '._'));
    }
    /**
     * 根据资源名获取模型类
     */
    public static function get($model)
    {
        if (!class_exists(($model))) {
            throw new Exception('资源不存在');
        }
        if (!is_subclass_of($model, Model::class)) {
            throw new Exception('资源不存在');
        }
        return new $model;
    }
    /**
     * 根据请求参数添加模型过滤条件
     */
    public static function filter(Collection $collect, Model | Builder $model): Model | Builder
    {
        return $collect->reduce(function (Model | Builder $theModel, $value, $key) {
            if ($key === static::keyUsedRelation) {
                return $theModel->with($value);
            } elseif ($key === static::keyUsedSelect) {
                return $theModel->select($value);
            } elseif (substr($key, 0, 1) === static::keyUsedFilter) {
                $action = substr($key, 1);
                if ($theModel->hasNamedScope($action)) {
                    return $theModel->$action($value);
                }
            } elseif (is_array($value)) {
                if ((substr($key, 0, 1) === static::keyUsedRange)) {
                    $theKey = substr($key, 1);
                    if (!empty($value[0])) $theModel = $theModel->where($theKey, '>=', $value[0]);
                    if (!empty($value[1])) $theModel = $theModel->where($theKey, '<=', $value[1]);
                    return $theModel;
                } else {
                    return $theModel->whereIn($key, $value);
                }
            } elseif (is_string($value) && (substr($value, 0, 1) === '%' || substr($value, -1) === '%')) {
                return $theModel->where($key, 'like', $value);
            }
            return $theModel->where($key, $value);
        }, $model);
    }
    /** 
     * 获取模型结构配置
     */
    public static function document(string $namespace): Collection
    {
        return collect(ClassLoader::getRegisteredLoaders()[base_path('vendor')]->getClassMap())
            ->filter(fn($path, $cls) => Str::contains($cls, $namespace))
            ->keys()
            ->map(function ($item) use ($namespace) {
                $name = Str::after($item, $namespace);
                $ref = new ReflectionClass($namespace . $name);
                if (empty($describe = $ref->getDocComment())) {
                    return null;
                }
                if (empty($response = ApiDocument::parse($describe))) {
                    return null;
                }
                ['table' => $table, 'connection' => $connection] = $ref->getDefaultProperties();
                $columns = collect(Schema::connection($connection)->getColumns($table))->map(function ($column) {
                    return [
                        'key' => $column['name'],
                        'name' => $column['comment'] ?: $column['name'],
                    ];
                });
                $methods = collect($ref->getMethods(ReflectionMethod::IS_PUBLIC));
                $parameters = $methods->filter(function (ReflectionMethod $item) {
                    return strpos($item->name, 'scope') === 0;
                })->map(function (ReflectionMethod $item) {
                    $parameter = ApiDocument::parse($item->getDocComment() ?: '');
                    $parameter['key'] = static::keyUsedFilter . lcfirst(str_replace('scope', '', $item->name));
                    return $parameter;
                })->values()->merge($columns)->push(
                    ['key' => '-', 'name' => '过滤输出字段'],
                    ['key' => '+', 'name' => '载入关联数据']
                );
                $relations = $methods->filter(function (ReflectionMethod $item) {
                    return Str::startsWith((string)$item->getReturnType(), 'Illuminate\Database\Eloquent\Relations');
                })->map(function (ReflectionMethod $item) {
                    return ApiDocument::parse($item->getDocComment() ?: '');
                })->filter()->values();
                $response['key'] = str_replace('\\', '.', $name);
                $response['parameters'] = $parameters;
                $response['relations'] = $relations;
                return $response;
            })->filter()->values();
    }
}
