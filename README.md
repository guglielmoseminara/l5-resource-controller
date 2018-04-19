# Resource Controller for Laravel 5

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Scrutinizer Code Quality][scrutinizer-code-quality]][link-scrutinizer]
[![Total Downloads][ico-downloads]][link-downloads]

Resource Controller for Laravel 5

## Install

Via Composer

``` bash
$ composer require rafflesargentina/l5-resource-controller
```

## Usage

Create a controller like you normally would and change it to extend ResourceController class. Then set $repository an $resourceName properties:

- $repository: The Repository class to instantiate.
- $resourceName: Set routes resource name.

Also you can set these optional properties:

- $alias: The alias for named routes.
- $theme: The location for themed views.
- $module: Set views vendor location prefix.
- $prefix : The vendor views prefix.
- $formRequest: The FormRequest class to instantiate (also take a look at [l5-action-based-form-request][link-abfr]).
- $useSoftDeletes: Define if model uses SoftDeletes.
- $infoFlashMessageKey: The info flash message key.
- $errorFlashMessageKey: The info flash message key.
- $successFlashMessageKey: The info flash message key.
- $warningFlashMessageKey: The info flash message key.

Example:

```php
<?php

namespace App\Http\Controllers;

use RafflesArgentina\ResourceController\ResourceController;

use App\Http\Requests\ArticleRequest;
use App\Repositories\ArticleRepository;

class ArticlesController extends ResourceController
{
    protected $repository = ArticleRepository::class;

    protected $formRequest = ArticleRequest::class;
    
    protected $resourceName = 'articles';
}
```
And that's it :)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email mario@raffles.com.ar instead of using the issue tracker.

## Credits

- [Mario Patronelli][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/rafflesargentina/l5-resource-controller.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/rafflesargentina/l5-resource-controller/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/rafflesargentina/l5-resource-controller.svg?style=flat-square
[scrutinizer-code-quality]: https://scrutinizer-ci.com/g/rafflesargentina/l5-resource-controller/badges/quality-score.png?b=master

[link-packagist]: https://packagist.org/packages/rafflesargentina/l5-resource-controller
[link-travis]: https://travis-ci.org/rafflesargentina/l5-resource-controller
[link-downloads]: https://packagist.org/packages/rafflesargentina/l5-resource-controller
[link-scrutinizer]: https://scrutinizer-ci.com/g/rafflesargentina/l5-resource-controller/?branch=master
[link-author]: https://github.com/patronelli87
[link-contributors]: ../../contributors
[link-abfr]: https://github.com/rafflesargentina/l5-action-based-form-request
