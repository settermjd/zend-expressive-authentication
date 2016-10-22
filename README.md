# Zend Expressive Authentication

This project provides PSR-7-compliant authentication middleware.

[![Build Status](https://img.shields.io/travis/PHP-DI/PHP-DI/master.svg?style=flat-square)](https://travis-ci.org/PHP-DI/PHP-DI)
[![Coverage Status](https://img.shields.io/coveralls/PHP-DI/PHP-DI/master.svg?style=flat-square)](https://coveralls.io/r/PHP-DI/PHP-DI?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/PHP-DI/PHP-DI.svg?style=flat-square)](https://scrutinizer-ci.com/g/PHP-DI/PHP-DI/?branch=master)
[![Latest Version](https://img.shields.io/github/release/PHP-DI/PHP-DI.svg?style=flat-square)](https://packagist.org/packages/PHP-DI/php-di)
[![Total Downloads](https://img.shields.io/packagist/dt/PHP-DI/PHP-DI.svg?style=flat-square)](https://packagist.org/packages/PHP-DI/php-di)

Here is an additional quick introduction, if necessary.

## Why?

This repository serves two purposes. Firstly, it serves as a supporting code repository for my talk [Build Powerful and Flexible Micro-Applications, with Zend Expressive](https://goo.gl/5Bysd2). And secondly, it provides a simplified set of PSR-7-compliant authentication middleware for a Zend Expressive (or other PSR-7) application.

If you’re in one of the sessions where I give this talk, then feel free to use this repository to both follow along or go further than I do in the talk, building and expanding on the content taught. If you need an drop-in authentication library, then feel free to use it, ignoring the tutorial/talk aspect.

## Installation

To install the code, depending on your needs, you can either clone the repository and browse through it, following along with the talk, by running:

```
git clone git@github.com:settermjd/zend-expressive-authentication.git
```

Alternatively, to integrate it with a PSR-7 compliant application, make use of Composer via the following command:

```
composer require settermjd/zend-expressive-authentication
```

As well as making the library available to your application as a core dependency, it will also copy the configuration files in place, assuming that you’re using it with Zend Expressive, and that your project directory structure follows the convention espoused by [the Zend Expressive Skeleton Installer](http://masterzendframework.com/...).

Currently, Zend Expressive is the only supported framework. Instructions for other PSR-7-compliant frameworks to follow.

## Usage

To make use of the project, there’s not much that you need to do. All that you need to do is to stack the `AuthenticationMiddleware` as part of the middleware stack for one, several, or all routes within your application. Here are two examples.

### Protecting a Single Route In An Application

```php
[
    'name' => 'home',
    'path' => '/',
    'middleware' =>
        [
            \App\Middleware\AuthenticationMiddleware::class,
            App\Action\HomePageAction::class,
        ],
    'allowed_methods' => ['GET'],
],
```

### Protecting All Routes In An Application

```php
'routing' => [
    'middleware' => [
        ApplicationFactory::ROUTING_MIDDLEWARE,
        \App\Middleware\AuthenticationMiddleware::class,
        ApplicationFactory::DISPATCH_MIDDLEWARE,
    ],
    'priority' => 1,
],
```

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.

