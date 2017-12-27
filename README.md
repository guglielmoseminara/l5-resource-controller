# l5-resource-controller

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

Resource Controller for Laravel 5

## Install

Via Composer

``` bash
$ composer require rafflesargentina/l5-resource-controller
```

## Usage

Create a controller like you normally would, but don't define any methods. Instead, change it to extend  ResourceController class. Then you should define a few protected properties. These are mandatory:

- $repository: The Repository class to instantiate.
- $resourceName: Set routes resource name.

And these are the optional:

- $alias: Set alias for named routes.
- $prefix : Set prefix for named routes.
- $module: Set views vendor location prefix.
- $formRequest: Set the FormRequest associate class to validate rules (take a look at [l5-action-based-form-request][link-abfr] ).

Example:

```php
<?php

namespace App\Http\Controllers;

use RafflesArgentina\ResourceController\ResourceController;

use App\Http\Requests\ArticleRequest;
use App\Repositories\ArticleRepository;

class ArticlesController extends ResourceController
{
    protected $alias = null;

    protected $module = 'admin';

    protected $prefix = null;
    
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

[link-packagist]: https://packagist.org/packages/rafflesargentina/l5-resource-controller
[link-travis]: https://travis-ci.org/rafflesargentina/l5-resource-controller
[link-downloads]: https://packagist.org/packages/rafflesargentina/l5-resource-controller
[link-author]: https://github.com/patronelli87
[link-contributors]: ../../contributors
[link-abfr]: https://github.com/rafflesargentina/l5-action-based-form-request
