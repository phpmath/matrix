---
title: Core concepts
intro: The few ideas you need to use Matrix well.
group: Getting started
order: 20
---

## Matrices are immutable

`Matrix` is a `final readonly` class. Once created, a matrix never changes. Methods such as `add()` or `scale()` return a new instance and leave the original untouched, so you can pass matrices around without defensive copies.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2], [3, 4]]);
$doubled = $matrix->scale(2);

echo $matrix;  // [[1, 2], [3, 4]]
echo $doubled; // [[2, 4], [6, 8]]
```

## Components are floats

Every number in a matrix is stored as a PHP `float`, even if you pass an `int`. This keeps all operations in one number type, but it also means results can carry small rounding errors. See [Comparison](comparison.md) for how to deal with that.

## Creating matrices

There are three ways to create a matrix. Each is a named constructor, because the real constructor is private.

`fromRows()` takes a list of rows. Every row must have the same number of columns.

```php
use PHPMath\Matrix\Matrix;

echo Matrix::fromRows([[1, 2, 3], [4, 5, 6]]); // [[1, 2, 3], [4, 5, 6]]
```

`zeros()` creates a matrix of the given size where every component is 0. `identity()` creates a square matrix with 1 on the diagonal and 0 everywhere else. The identity matrix is the matrix version of the number 1: multiplying by it changes nothing.

```php
use PHPMath\Matrix\Matrix;

echo Matrix::zeros(2, 3);  // [[0, 0, 0], [0, 0, 0]]
echo Matrix::identity(3);  // [[1, 0, 0], [0, 1, 0], [0, 0, 1]]
```

Invalid input throws an [`InvalidMatrixException`](errors.md). This covers empty input, rows of different length, values that are not an `int` or `float` (such as a numeric string), and numbers that are `NAN` or infinite.

```php
use PHPMath\Matrix\Exception\InvalidMatrixException;
use PHPMath\Matrix\Matrix;

try {
    Matrix::fromRows([[1, 2], [3]]);
} catch (InvalidMatrixException $exception) {
    echo $exception->getMessage(); // All rows of a matrix must have the same number of columns.
}
```

> [!NOTE]
> Rows and columns are renumbered from zero. `fromRows()` ignores the keys of the array you pass, so `['a' => ['x' => 1]]` works the same as `[[1]]`.

> [!WARNING]
> The components must be `int` or `float`. A numeric string such as `'1'` throws an `InvalidMatrixException`, it is not converted.

## Reading values

`rows()` and `columns()` give the size. `get()` returns one component. Positions start at 0, with the row first.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

echo $matrix->rows();    // 2
echo $matrix->columns(); // 3
echo $matrix->get(1, 2); // 6
```

`toArray()` returns all components as a list of rows of floats.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2]]);

var_dump($matrix->toArray()); // array(1) { [0]=> array(2) { [0]=> float(1) [1]=> float(2) } }
```

A matrix can also be cast to a string, which is handy for debugging. Rows are written as `[1, 2]`, and whole numbers lose their decimal part. Numbers are written as plain decimals with at most 14 decimals, never with an exponent or trailing zeros, and `-0.0` is written as `0`.

```php
use PHPMath\Matrix\Matrix;

echo Matrix::fromRows([[1.5, -2], [0.25, 4]]);                // [[1.5, -2], [0.25, 4]]
echo Matrix::fromRows([[0.0000001, -0.0], [1 / 3, 1.0E+20]]); // [[0.0000001, 0], [0.33333333333333, 100000000000000000000]]
```

> [!NOTE]
> The string form does not depend on the `precision` setting in `php.ini`. It rounds to 14 decimals, so use `toArray()` when you need the exact values.

## Overflow is not checked

Operations such as `add()`, `scale()`, `multiply()` and `inverse()` do not check their results. When a number gets too large for a float, the component becomes `INF` (or `NAN`, for example for `INF * 0`) and no exception is thrown. This is the same policy as in the other phpmath libraries: the input is validated, the result is not. Call `isFinite()` on a result when it can overflow.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1.0E+308, 1]]);
$result = $matrix->scale(10);

var_dump($matrix->isFinite()); // bool(true)
var_dump($result->isFinite()); // bool(false)
echo $result;                  // [[INF, 10]]
```

## Errors

The library throws its own exceptions for problems with sizes, positions and singular matrices. They are listed on the [Errors](errors.md) page.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\IndexOutOfRangeException;

$matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

try {
    $matrix->get(2, 0);
} catch (IndexOutOfRangeException $exception) {
    echo $exception->getMessage(); // Position (2, 0) is outside of a 2x3 matrix.
}
```
