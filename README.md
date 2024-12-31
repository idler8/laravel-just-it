# Laravel Just it

## Introduction

Rapid development toolkit for application programming interfaces based on Laravel.  
Use it to avoid repetitive CRUD development and not hinder your normal development habits.  
Use it to generate standard documentation for your API.

## Getting Started

`composer require idler8/laravel-just-it`

```php
Route::middleware('api')
    ->namespace('Justit')
    ->prefix('r')
    ->group(function () {
        Route::get("/", function(){
            $apis = \Justit\ApiDocument::document('api');
            $resources = \Justit\Resource::document();
            return [ 'apis' => $apis,'resources'=> $resources];
        });

        Route::post("{name}", "Controller@store");
        Route::delete("{name}/{id}", "Controller@destroy");
        Route::put("{name}/{id}", "Controller@update");
        Route::get("{name}/{id}", "Controller@show");
        Route::get("{name}", "Controller@index");
        Route::get("{name}/{pre_page}/{page}", "Controller@paginate");
    });
```
For the configuration of Route, please refer to the official documentation of [Laravel](https://laravel.com/docs/routing#parameters-and-dependency-injection)  
For more practical methods, please refer to the [source code](/src) or [test cases](/docker/tests)

## Test Environment

```bash
# Run a Docker testing environment
docker run --rm -it -v $PWD:/app $(docker build -f ./docker/Dockerfile . -q)
# Copy test cases & Run test
php artisan justit
```
If files in the [docker](/docker) directory are deleted, it is recommended to restart the Docker based testing environment.

## License

Laravel Just it is open-sourced software licensed under the [MIT license](LICENSE.md).

## Why develop it
This is one of the achievements of my years of development experience. I have used it in many projects and it has helped me develop multiple new full stack projects simultaneously in a very short period of time. I included it in my college graduation thesis, but my teacher criticized it as useless. I want to know the public's opinion on this, so I put it here.
