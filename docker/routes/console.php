<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;


Artisan::command('justit:require', function () {
    Process::run(`composer config repositories.local '{"type": "path", "url": "/app"}'`);
    Process::run(`composer require laravel/justit --no-scripts`);
})->purpose('初始化测试系统');

Artisan::command('justit:copied', function () {
    Process::run(`cp -rf /app/docker/* ./`);
})->purpose('初始化测试系统');

Artisan::command('justit', function () {
    $this->call('justit:copied');
    $this->call('test');
});
