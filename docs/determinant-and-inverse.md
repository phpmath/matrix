---
title: Determinant and inverse
intro: Compute the determinant of a square matrix and invert it.
group: Guides
order: 50
---

## Determinant

The determinant is a single number calculated from a square matrix. It tells you, among other things, whether the matrix can be inverted: a determinant of 0 means it cannot. For a 2x2 matrix it is `a*d - b*c`. The library works for any square size and returns a float.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2], [3, 4]]);
$diagonal = Matrix::fromRows([[2, 0, 0], [0, 3, 0], [0, 0, 4]]);

echo $matrix->determinant();   // -2
echo $diagonal->determinant(); // 24
```

A matrix that is not square throws [`NotSquareException`](errors.md).

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\NotSquareException;

try {
    Matrix::zeros(2, 3)->determinant();
} catch (NotSquareException $exception) {
    echo $exception->getMessage(); // Expected a square matrix, got a 2x3 matrix.
}
```

> [!NOTE]
> The determinant is calculated with floats, so it can be slightly off. The matrix `[[2, 1, 1], [1, 3, 2], [1, 0, 0]]` has a determinant of exactly -1, but the library returns `-0.9999999999999998`. Compare results with a tolerance, see [Comparison](comparison.md).

## Singular matrices

A singular matrix is a square matrix with a determinant of 0. It has no inverse. This happens when one row is a multiple or a combination of the other rows. `isSingular()` tells you, and it follows the same rule as `determinant()` and `inverse()`.

```php
use PHPMath\Matrix\Matrix;

$singular = Matrix::fromRows([[1, 2], [2, 4]]);

echo $singular->determinant();     // 0
var_dump($singular->isSingular()); // bool(true)
```

Rounding errors would leave a tiny number instead of 0 for many singular matrices, such as the rows `1 2 3`, `4 5 6` and `7 8 9`. To avoid that, `determinant()`, `inverse()` and `isSingular()` all use one rule: a pivot (a number the algorithm divides by) at or below `1e-12` times the largest absolute component of the matrix counts as zero. A singular matrix then gives exactly `0.0`.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

var_dump($matrix->determinant()); // float(0)
var_dump($matrix->isSingular()); // bool(true)
```

A matrix that is not square throws [`NotSquareException`](errors.md) when you call `isSingular()`.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\NotSquareException;

try {
    Matrix::zeros(2, 3)->isSingular();
} catch (NotSquareException $exception) {
    echo $exception->getMessage(); // Expected a square matrix, got a 2x3 matrix.
}
```

## Inverse

The inverse of a matrix is the matrix that undoes it. Multiplying a matrix by its inverse gives the identity matrix. The inverse is only defined for square, non-singular matrices.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[4, 7], [2, 6]]);
$inverse = $matrix->inverse();

echo $inverse;                     // [[0.6, -0.7], [-0.2, 0.4]]
echo $matrix->multiply($inverse); // [[1, 0], [0, 1]]
```

Because of floating point rounding, the product is not always exactly the identity matrix. Check it with a tolerance.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[4, 7], [2, 6]]);
$product = $matrix->multiply($matrix->inverse());

var_dump($product->equals(Matrix::identity(2), 1e-12)); // bool(true)
```

## Singular matrices throw

`inverse()` throws [`SingularMatrixException`](errors.md) when the matrix has no inverse. The matrix counts as singular by the rule described above (a pivot at or below `1e-12` times the largest absolute component), so this also catches matrices that are singular only because of rounding. `isSingular()` tells you in advance whether `inverse()` will throw.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\SingularMatrixException;

try {
    Matrix::fromRows([[1, 2], [2, 4]])->inverse();
} catch (SingularMatrixException $exception) {
    echo $exception->getMessage(); // The matrix is singular and cannot be inverted.
}
```

A matrix that is not square throws [`NotSquareException`](errors.md) before anything else.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\NotSquareException;

try {
    Matrix::fromRows([[1, 2, 3], [4, 5, 6]])->inverse();
} catch (NotSquareException $exception) {
    echo $exception->getMessage(); // Expected a square matrix, got a 2x3 matrix.
}
```

> [!NOTE]
> `determinant()`, `inverse()` and `isSingular()` agree: for `[[1, 2, 3], [4, 5, 6], [7, 8, 9]]` the determinant is exactly `0.0`, `isSingular()` is `true` and `inverse()` throws `SingularMatrixException`. Because the threshold is relative, very large or very small matrices are judged by their own scale.

## Solving equations

A system of linear equations such as `2x + y = 3` and `x + 3y = 5` can be written as `A * v = b`. Multiply both sides by the inverse of `A` to get `v`.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[2, 1], [1, 3]]);
$b = Matrix::fromRows([[3], [5]]);

echo $a->inverse()->multiply($b); // [[0.8], [1.4]]
```

So `x = 0.8` and `y = 1.4`.
