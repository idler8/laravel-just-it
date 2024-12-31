# Laravel Just it

## Introduction

Rapid development kit for application interfaces based on Laravel.

## Test Environment

```bash
# Run a Docker testing environment
docker run --rm -it -v $PWD:/app $(docker build -f ./docker/Dockerfile . -q)
# Run test
php artisan justit
```

## License

Laravel Just it is open-sourced software licensed under the [MIT license](LICENSE.md).
