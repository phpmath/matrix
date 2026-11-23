---
title: Shape and transpose
intro: Read the size of a matrix, flip it, and take the trace of a square matrix.
group: Guides
order: 40
---

## Size and square matrices

The size of a matrix is its number of rows by its number of columns. A square matrix has as many rows as columns. Some operations, such as `trace()`, `determinant()` and `inverse()`, only work on square matrices.

```php
use PHPMath\Matrix\Matrix;

$square = Matrix::fromRows([[1, 2], [3, 4]]);
$wide = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

var_dump($square->isSquare()); // bool(true)
var_dump($wide->isSquare());   // bool(false)
```

## Transpose

The transpose of a matrix swaps its rows and columns. The first row becomes the first column, the second row becomes the second column, and so on. A 2x3 matrix becomes a 3x2 matrix.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

echo $matrix->transpose(); // [[1, 4], [2, 5], [3, 6]]
```

Transposing twice gives the original matrix back.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

var_dump($matrix->transpose()->transpose()->equals($matrix)); // bool(true)
```

The transpose of a product reverses the order of the factors: the transpose of `a * b` equals the transpose of `b` times the transpose of `a`.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$b = Matrix::fromRows([[0, 1], [1, 0]]);

echo $a->multiply($b)->transpose();               // [[2, 4], [1, 3]]
echo $b->transpose()->multiply($a->transpose());  // [[2, 4], [1, 3]]
```

## Trace

The trace of a square matrix is the sum of the components on its main diagonal, which runs from the top left to the bottom right. It returns a float.

```php
use PHPMath\Matrix\Matrix;

$matrix = Matrix::fromRows([[1, 2], [3, 4]]);

echo $matrix->trace(); // 5
```

A matrix that is not square throws [`NotSquareException`](errors.md).

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\NotSquareException;

$wide = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

try {
    $wide->trace();
} catch (NotSquareException $exception) {
    echo $exception->getMessage(); // Expected a square matrix, got a 2x3 matrix.
}
```
