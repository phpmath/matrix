---
title: Errors and exceptions
intro: What can go wrong and how to catch it.
group: Reference
order: 80
---

## The MatrixException interface

Every exception the library throws on purpose implements the marker interface `PHPMath\Matrix\Exception\MatrixException`, which extends `Throwable`. Catch it to handle any error from this library in one place, without catching unrelated errors from your own code.

Each exception is a `final` class named after its cause. It also extends exactly one standard PHP exception, so you can catch it as narrowly or as broadly as you like. The library never throws a plain PHP exception on purpose.

## Library exceptions

This table lists every exception, the PHP exception it extends and the moment it is thrown. All classes live in the `PHPMath\Matrix\Exception` namespace and implement `MatrixException`.

| Exception | Extends | Thrown when |
|---|---|---|
| `InvalidMatrixException` | `InvalidArgumentException` | `fromRows()` gets no rows, an empty row, rows of different length or a value that is not an `int` or `float`, or is `NAN` or infinite. `identity()` gets a size below 1. `zeros()` gets rows or columns below 1. |
| `InvalidToleranceException` | `InvalidArgumentException` | The tolerance of `equals()` is negative or `NAN`. |
| `InvalidTransformationException` | `InvalidArgumentException` | A distance, factor or angle of `translation()`, `scaling()`, `rotationX()`, `rotationY()`, `rotationZ()` or `rotation()` is `NAN` or infinite. |
| `DimensionMismatchException` | `InvalidArgumentException` | The sizes of the matrices or the vector do not fit the operation. |
| `NotSquareException` | `InvalidArgumentException` | An operation needs a square matrix. |
| `ZeroRotationAxisException` | `DomainException` | The axis of `rotation()` is the zero vector, so no rotation is defined. |
| `SingularMatrixException` | `DomainException` | The matrix has no inverse. |
| `IndexOutOfRangeException` | `OutOfRangeException` | A row or column does not exist. |

## Which method throws what

This table lists every case where a method throws one of the library exceptions.

| Method | Exception | Condition |
|---|---|---|
| `fromRows()` | `InvalidMatrixException` | There are no rows, a row is empty, the rows have different lengths, or a value is not an `int` or `float`, or is `NAN` or infinite. |
| `identity()` | `InvalidMatrixException` | The size is smaller than 1. |
| `zeros()` | `InvalidMatrixException` | The rows or columns are smaller than 1. |
| `equals()` | `InvalidToleranceException` | The tolerance is negative or `NAN`. |
| `translation()`, `scaling()`, `rotationX()`, `rotationY()`, `rotationZ()`, `rotation()` | `InvalidTransformationException` | A distance, factor or angle is `NAN` or infinite. |
| `rotation()` | `ZeroRotationAxisException` | The axis is the zero vector. |
| `add()`, `subtract()` | `DimensionMismatchException` | The matrices have a different size. |
| `multiply()` | `DimensionMismatchException` | The columns of the left matrix differ from the rows of the right matrix. |
| `transform()` | `DimensionMismatchException` | The vector has a different number of components than the matrix has columns. |
| `transform()` | `InvalidComponentException` (from `phpmath/vector`) | A component of the result overflows to `INF`. |
| `trace()`, `determinant()`, `isSingular()`, `inverse()` | `NotSquareException` | The matrix is not square. |
| `inverse()` | `SingularMatrixException` | The matrix has no inverse. |
| `get()` | `IndexOutOfRangeException` | The row or column is outside the matrix. |

## Type errors

Methods have strict types. Passing a value of the wrong type, such as a string to `scale()` or an array to `add()`, throws a PHP `TypeError`. The values inside the rows of `fromRows()` are checked by the library itself, so a numeric string there throws an `InvalidMatrixException`. This is not a library exception and usually points to a bug in the calling code.

## Handling errors

To handle every error from the library in one place, catch `MatrixException`. Catch a specific class when you want to react to one cause only, for example `SingularMatrixException` when a matrix may have no inverse.

```php
use PHPMath\Matrix\Exception\MatrixException;
use PHPMath\Matrix\Matrix;

$attempts = [
    fn () => Matrix::fromRows([[1, 2], [3]]),
    fn () => Matrix::fromRows([[1, 2], [3, 4]])->add(Matrix::identity(3)),
    fn () => Matrix::fromRows([[1, 2], [2, 4]])->inverse(),
    fn () => Matrix::identity(2)->get(5, 0),
];

foreach ($attempts as $attempt) {
    try {
        $attempt();
    } catch (MatrixException $exception) {
        echo $exception::class . ': ' . $exception->getMessage() . "\n";
    }
}
// PHPMath\Matrix\Exception\InvalidMatrixException: All rows of a matrix must have the same number of columns.
// PHPMath\Matrix\Exception\DimensionMismatchException: Expected a 2x2 matrix, got a 3x3 matrix.
// PHPMath\Matrix\Exception\SingularMatrixException: The matrix is singular and cannot be inverted.
// PHPMath\Matrix\Exception\IndexOutOfRangeException: Position (5, 0) is outside of a 2x2 matrix.
```
