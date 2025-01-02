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

```php
/**
 * This method is used to extract API documentation
 * Methods without general annotations will not be extracted
 */
dd(\Justit\ApiDocument::document('api'/** prefix of middleware */));
[[
    "name" => "From the first line of the general comment on the method",
    "describe" => "From the latter line of the general comment on the method",
    "parameters" =>  [[
        "key" => "The first part from @param",
        "name" => "The latter part from @param"
    ]],
    "key" => "Controller@method",
    "urls" => ["METHOD:uri"]
]]
/**
 * This method extracts Model class documents from all app/Models directories
 * Classes that are not Model or have no general annotations will not be extracted
 */
dd(\Justit\Resource::document('App\\Models\\'/** prefix of namespace */));
[[
    "name" => "From the first line of the general comment on the class"
    "key" => "From the latter line of the general comment on the class"
    "parameters" => [[
        "key"=>"Data table field names",
        "name"=>"Data table field comments"
    ],[
        "key"=>"(:)prefix to scope name",
        "name"=>"Function Comments"
    ],[
        "key"=>"-/+",
        "name"=>"Special parameters"
    ]],
    "relations" => [[
        "key"=>"The name of the method that will explicitly output a Relation(hasOne/hasMany)",
        "name"=>"Function Comments "
    ]]
]]
```

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
