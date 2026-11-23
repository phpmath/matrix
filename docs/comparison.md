---
title: Comparison
intro: Compare matrices exactly or with a tolerance for rounding errors.
group: Guides
order: 60
---

## Comparing with equals

`equals()` returns `true` when two matrices have the same size and every pair of components is equal. Matrices of different sizes are never equal, and no exception is thrown.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);

var_dump($a->equals(Matrix::fromRows([[1, 2], [3, 4]]))); // bool(true)
var_dump($a->equals(Matrix::zeros(3, 3)));                // bool(false)
```

> [!WARNING]
> Use `equals()` instead of `==`. The `==` operator on two `Matrix` objects compares their internal arrays, while `===` is only true for the very same instance.

## Tolerance

Floats cannot store every decimal exactly, so a result such as `0.1 + 0.2` is not exactly `0.3`. The optional second argument is a tolerance: the largest difference allowed per component. The default is 0, which means an exact match.

```php
use PHPMath\Matrix\Matrix;

$sum = Matrix::fromRows([[0.1 + 0.2]]);
$expected = Matrix::fromRows([[0.3]]);

var_dump($sum->equals($expected));       // bool(false)
var_dump($sum->equals($expected, 1e-9)); // bool(true)
```

A tolerance of `0.1` accepts components that differ by up to 0.1.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$close = Matrix::fromRows([[1.05, 2], [3, 4]]);

var_dump($a->equals($close, 0.1)); // bool(true)
```

A tolerance that is negative or `NAN` throws an [`InvalidToleranceException`](errors.md).

```php
use PHPMath\Matrix\Exception\InvalidToleranceException;
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);

try {
    $a->equals($a, -0.5);
} catch (InvalidToleranceException $exception) {
    echo $exception->getMessage(); // The tolerance must be zero or greater.
}
```

## Checking an inverse

A common use of a tolerance is to check that a matrix times its inverse is the identity matrix.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2], [3, 4]]);
$product = $matrix->multiply($matrix->inverse());

var_dump($product->equals(Matrix::identity(2)));       // bool(false)
var_dump($product->equals(Matrix::identity(2), 1e-9)); // bool(true)
```
