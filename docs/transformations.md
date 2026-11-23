---
title: Transformations
intro: Move, scale and rotate points and directions with 4x4 matrices.
group: Guides
order: 55
---

## Transforming a vector

`transform()` multiplies a matrix by a vector from `phpmath/vector`. The vector must have as many components as the matrix has columns. The result has as many components as the matrix has rows, and it is a `Vector2`, `Vector3` or `Vector4` when it has two, three or four components. Any other size gives a plain `Vector`.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector2;

$quarterTurn = Matrix::fromRows([[0, -1], [1, 0]]);

echo $quarterTurn->transform(new Vector2(1, 0)); // [0, 1]
```

A vector of the wrong size throws `DimensionMismatchException`. A vector never holds `INF` or `NAN`, so if a component of the result overflows, `transform()` throws `InvalidComponentException` from `phpmath/vector`.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Matrix\Exception\DimensionMismatchException;
use PHPMath\Vector\Vector3;

try {
    Matrix::identity(2)->transform(new Vector3(1, 2, 3));
} catch (DimensionMismatchException $exception) {
    echo $exception->getMessage(); // Cannot transform a vector with 3 components by a 2x2 matrix.
}
```

## Homogeneous coordinates

Moving a point cannot be written as a 3x3 matrix, so the transformation matrices in this library are 4x4 and work on a `Vector4`. Its fourth component, w, is 1 for a point and 0 for a direction. See [Four dimensions](/vector/) in the vector documentation for how to create and convert these vectors.

```php
use PHPMath\Vector\Vector3;
use PHPMath\Vector\Vector4;

$point = Vector4::fromVector3(new Vector3(1, 2, 3));     // [1, 2, 3, 1]
$direction = Vector4::fromVector3(new Vector3(1, 2, 3), 0); // [1, 2, 3, 0]
```

All angles are in radians. The matrices act on column vectors, and the coordinate system is right-handed: a positive rotation around an axis turns counterclockwise when you look against that axis.

## Translation

`Matrix::translation()` moves a point by a distance along each axis. A direction is not moved, because its w component is 0.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector4;

$move = Matrix::translation(10, 20, 30);

echo $move;                                   // [[1, 0, 0, 10], [0, 1, 0, 20], [0, 0, 1, 30], [0, 0, 0, 1]]
echo $move->transform(new Vector4(1, 1, 1, 1)); // [11, 21, 31, 1]
echo $move->transform(new Vector4(1, 1, 1, 0)); // [1, 1, 1, 0]
```

## Scaling

`Matrix::scaling()` stretches or shrinks along each axis. Use the same factor three times for a uniform scale, or a negative factor to mirror along an axis.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector4;

echo Matrix::scaling(2, 3, 4)->transform(new Vector4(1, 1, 1, 1)); // [2, 3, 4, 1]
```

## Rotation around an axis

`rotationX()`, `rotationY()` and `rotationZ()` rotate around one of the coordinate axes. Because of floating point, a result that should be exactly 0 can come out as a tiny number such as `6.1232339957368E-17`.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector4;

$quarterTurn = M_PI / 2;

echo Matrix::rotationZ($quarterTurn)->transform(new Vector4(1, 0, 0, 1)); // [6.1232339957368E-17, 1, 0, 1]
```

`rotation()` rotates around any axis through the origin. The axis is a `Vector3`. It does not have to be a unit vector, but it must not be the zero vector. Rotating a third of a turn around the diagonal (1, 1, 1) moves the x axis onto the y axis.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector3;
use PHPMath\Vector\Vector4;

$matrix = Matrix::rotation(new Vector3(1, 1, 1), 2 * M_PI / 3);

$result = $matrix->transform(new Vector4(1, 0, 0, 1));

echo implode(', ', array_map(fn (float $value): float => round($value, 12) + 0.0, $result->toArray())); // 0, 1, 0, 1
```

## Combining transformations

Multiply matrices to combine transformations into one. Matrices act on column vectors, so the matrix on the right is applied first. Order matters: move then rotate gives a different result than rotate then move.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector4;

$move = Matrix::translation(1, 0, 0);
$turn = Matrix::rotationZ(M_PI / 2);
$origin = new Vector4(0, 0, 0, 1);

$moveThenTurn = $turn->multiply($move);
$turnThenMove = $move->multiply($turn);

// move first: (0, 0, 0) -> (1, 0, 0) -> (0, 1, 0)
echo implode(', ', array_map(fn (float $v): float => round($v, 12) + 0.0, $moveThenTurn->transform($origin)->toArray())); // 0, 1, 0, 1

// turn first: (0, 0, 0) -> (0, 0, 0) -> (1, 0, 0)
echo implode(', ', array_map(fn (float $v): float => round($v, 12) + 0.0, $turnThenMove->transform($origin)->toArray())); // 1, 0, 0, 1
```

## Undoing a transformation

The inverse of a transformation matrix undoes it. Apply the inverse after the matrix to get the original point back, within floating point precision.

```php
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector4;

$matrix = Matrix::rotationZ(0.5)->multiply(Matrix::translation(1, 2, 3));
$point = new Vector4(4, 5, 6, 1);

$back = $matrix->inverse()->transform($matrix->transform($point));

echo implode(', ', array_map(fn (float $v): float => round($v, 9) + 0.0, $back->toArray())); // 4, 5, 6, 1
```

## Invalid parameters

Every factory rejects a distance, factor or angle that is `NAN` or infinite, and `rotation()` also rejects the zero vector as axis. A non-finite value throws an [`InvalidTransformationException`](errors.md) and a zero axis throws a [`ZeroRotationAxisException`](errors.md). See [Errors and exceptions](errors.md).
