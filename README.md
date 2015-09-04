# StackStaticFiles #

[![Build Status](https://travis-ci.org/Matthimatiker/StackStaticFiles.svg?branch=master)](https://travis-ci.org/Matthimatiker/StackStaticFiles)
[![Coverage Status](https://coveralls.io/repos/Matthimatiker/StackStaticFiles/badge.svg?branch=master&service=github)](https://coveralls.io/github/Matthimatiker/StackStaticFiles?branch=master)

Simple [StackPHP](http://stackphp.com/) middleware that serves files from a given directory.
If the requested file does not exist, then the request is delegated to the next kernel.

## Initialization Tasks (remove this block once you are done) ##

- Publish at [Packagist](https://packagist.org/)
- Create webhook that pushes repository updates to [Packagist](https://packagist.org/)

## Motivation ##

This middleware has been created to be able to use [php-pm/php-pm](https://github.com/php-pm/php-pm)
with [php-pm/php-pm-httpkernel](https://github.com/php-pm/php-pm-httpkernel) as a standalone server.
It avoided the need of nginx for serving static files.

## Installation ##

This library is installed via [Composer](http://getcomposer.org/).
Add the following dependency to your ``composer.json``:

    "require" :  {
        // ...
        "matthimatiker/stack-static-files": "^0.0.1"
    }

## Concept ##

## Usage ##

You can use [stack/builder](https://github.com/stackphp/builder) to compose your middleware stack:

    $stack = (new Builder())->push(StaticFiles::class, __DIR__ . '/public-files');
    $app = $stack->resolve($kernel);

Alternatively, you can combine kernel and middleware manually:

    $app = new StaticFiles($kernel,  __DIR__ . '/public-files');

## Known Issues ##

