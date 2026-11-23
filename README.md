# matrix

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![CI][ico-ci]][link-ci]
[![Total Downloads][ico-downloads]][link-downloads]

A PHP library to work with immutable mathematical matrices of real numbers.

## Documentation

The full documentation is at https://phpmath.github.io/matrix/. Every change is listed in the [changelog](CHANGELOG.md) and every release on the [releases page](https://github.com/phpmath/matrix/releases).

## Install

```bash
composer require phpmath/matrix
```

## Requirements

- PHP 8.4 or newer
- `phpmath/vector`, which Composer installs for you

## Usage

Every matrix is an immutable value object. Operations never change the instance they are
called on, they return a new instance.

``` php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$b = Matrix::identity(2);

$sum = $a->add($b);          // [[2, 2], [3, 5]]
$product = $a->multiply($b); // [[1, 2], [3, 4]]
$det = $a->determinant();    // -2
$inverse = $a->inverse();    // [[-2, 1], [1.5, -0.5]]

echo $a->transpose();        // [[1, 3], [2, 4]]
```

## Features

* Creating matrices with `fromRows()`, `identity()` and `zeros()`.
* Reading with `rows()`, `columns()`, `get()`, `toArray()` and `isSquare()`.
* `add()`, `subtract()`, `scale()`, `multiply()` (matrix product) and `transpose()`.
* `trace()`, `determinant()` (LU decomposition with partial pivoting) and `inverse()`.
* Transforming vectors with `transform()`, and building 4x4 transformation matrices with `translation()`,
  `scaling()`, `rotationX()`, `rotationY()`, `rotationZ()` and `rotation()`.
* Comparing with `equals()`, which accepts a tolerance.
* Specific exceptions, all implementing `MatrixException`.

## Development

```bash
composer test
composer test-coverage
composer analyse
composer check-style
composer fix-style
```

## Contributing

All contributions are welcome. Open an issue or a pull request on GitHub. Please follow the coding standard and add tests.

## Security

If you discover a security issue, please create an issue in the issue tracker.

## Credits

- [Walter Tamboer](https://github.com/waltertamboer)
- [All contributors](https://github.com/phpmath/matrix/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/phpmath/matrix.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-ci]: https://img.shields.io/github/actions/workflow/status/phpmath/matrix/ci.yml?branch=master&style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/phpmath/matrix.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/phpmath/matrix
[link-ci]: https://github.com/phpmath/matrix/actions/workflows/ci.yml
[link-downloads]: https://packagist.org/packages/phpmath/matrix/stats
