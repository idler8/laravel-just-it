<?php

namespace Justit;

use Illuminate\Support\Str;

/** 获取结构配置 */
class Configuration
{
    public static function base()
    {
        return app_path('Models');
    }
    public static function isResourceApi(string $controller)
    {
        return Str::contains($controller, 'Controller@');
    }
}
