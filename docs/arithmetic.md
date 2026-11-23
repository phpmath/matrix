---
title: Arithmetic
intro: Add, subtract, scale and multiply matrices.
group: Guides
order: 30
---

## Adding and subtracting

Both methods work component by component, so the two matrices must have the same number of rows and columns. A different size throws [`DimensionMismatchException`](errors.md).

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$b = Matrix::fromRows([[10, 20], [30, 40]]);

echo $a->add($b);      // [[11, 22], [33, 44]]
echo $b->subtract($a); // [[9, 18], [27, 36]]
```

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\DimensionMismatchException;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$wide = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

try {
    $wide->add($a);
} catch (DimensionMismatchException $exception) {
    echo $exception->getMessage(); // Expected a 2x3 matrix, got a 2x2 matrix.
}
```

## Scaling

Scaling multiplies every component by one number, called a scalar. The factor is a plain PHP number, not a matrix. Use a negative factor to flip all signs.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);

echo $a->scale(2.5); // [[2.5, 5], [7.5, 10]]
echo $a->scale(-1);  // [[-1, -2], [-3, -4]]
```

## Multiplying matrices

Matrix multiplication is not component by component. Each component of the result is the sum of a row of the first matrix multiplied by a column of the second. This only works when the number of columns of the first matrix equals the number of rows of the second. The result has the rows of the first matrix and the columns of the second.

```php
use PHPMath\Matrix\Matrix;

$x = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]); // 2x3
$y = Matrix::fromRows([[1, 0], [0, 1], [1, 1]]); // 3x2

echo $x->multiply($y); // [[4, 5], [10, 11]]
```

A row times a column gives a single number, wrapped in a 1x1 matrix.

```php
use PHPMath\Matrix\Matrix;

$row = Matrix::fromRows([[1, 2, 3]]);
$column = Matrix::fromRows([[4], [5], [6]]);

echo $row->multiply($column); // [[32]]
```

If the sizes do not fit, `multiply()` throws [`DimensionMismatchException`](errors.md).

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\DimensionMismatchException;

$x = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

try {
    $x->multiply($x);
} catch (DimensionMismatchException $exception) {
    echo $exception->getMessage(); // Cannot multiply a 2x3 matrix by a 2x3 matrix.
}
```

## Order matters

Unlike numbers, `a * b` and `b * a` are usually different matrices. Swapping the order can even change the size of the result.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$swap = Matrix::fromRows([[0, 1], [1, 0]]);

echo $a->multiply($swap); // [[2, 1], [4, 3]]
echo $swap->multiply($a); // [[3, 4], [1, 2]]
```

Multiplying by the identity matrix returns the same values, in either order.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);

echo $a->multiply(Matrix::identity(2)); // [[1, 2], [3, 4]]
```
