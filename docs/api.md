---
title: API reference
intro: Every public method of Matrix, grouped by purpose.
group: Reference
order: 70
layout: reference
---

Every method returns a new `Matrix` unless the return type says otherwise. All methods belong to `PHPMath\Matrix\Matrix`. Components are always floats, and positions start at 0.

## Construction

Create a matrix from rows, or as a zero or identity matrix. The constructor itself is private, so use these named constructors.

### fromRows()

```php
public static function fromRows(array $rows): self
```

Creates a matrix from a list of rows. The keys of the array and of each row are ignored. Ints are converted to floats.

- **Parameters:** `$rows` a non-empty list of non-empty rows of equal length, holding `int` or `float` values
- **Returns:** `Matrix`
- **Throws:** [InvalidMatrixException](errors.md) if there are no rows, a row is empty, the rows have different lengths, or a value is not an `int` or `float`, or is `NAN` or infinite

### identity()

```php
public static function identity(int $size): self
```

Creates a square matrix with 1 on the main diagonal and 0 elsewhere.

- **Parameters:** `$size` the number of rows and columns, at least 1
- **Returns:** `Matrix`
- **Throws:** [InvalidMatrixException](errors.md) if the size is smaller than 1

### zeros()

```php
public static function zeros(int $rows, int $columns): self
```

Creates a matrix where every component is 0.

- **Parameters:** `$rows` the number of rows, at least 1; `$columns` the number of columns, at least 1
- **Returns:** `Matrix`
- **Throws:** [InvalidMatrixException](errors.md) if the rows or the columns are smaller than 1

## Reading values

Read the size, single components, or the whole matrix.

### rows()

```php
public function rows(): int
```

The number of rows.

- **Returns:** `int`

### columns()

```php
public function columns(): int
```

The number of columns.

- **Returns:** `int`

### get()

```php
public function get(int $row, int $column): float
```

Returns one component. Both positions start at 0.

- **Parameters:** `$row` the row index; `$column` the column index
- **Returns:** `float`
- **Throws:** [IndexOutOfRangeException](errors.md) if the row or column does not exist (negative indexes are out of range too)

### toArray()

```php
public function toArray(): array
```

Returns all components as a list of rows.

- **Returns:** `list<list<float>>`

### __toString()

```php
public function __toString(): string
```

Writes the matrix as text, for example `[[1, 2], [3, 4]]`. Numbers are plain decimals with at most 14 decimals, without an exponent, without trailing zeros and never as a negative zero, so `0.0000001` stays `0.0000001` and `-0.0` becomes `0`. The text does not depend on PHP's `precision` setting. Non-finite components are written as `INF`, `-INF` or `NAN`.

- **Returns:** `string`

## Arithmetic

Add, subtract, scale and multiply.

### add()

```php
public function add(Matrix $other): self
```

Adds a matrix component by component.

- **Parameters:** `$other` a matrix of the same size
- **Returns:** `Matrix`
- **Throws:** [DimensionMismatchException](errors.md) if the sizes differ

### subtract()

```php
public function subtract(Matrix $other): self
```

Subtracts a matrix component by component.

- **Parameters:** `$other` a matrix of the same size
- **Returns:** `Matrix`
- **Throws:** [DimensionMismatchException](errors.md) if the sizes differ

### scale()

```php
public function scale(float $factor): self
```

Multiplies every component by a number.

- **Parameters:** `$factor` the scalar
- **Returns:** `Matrix`

### multiply()

```php
public function multiply(Matrix $other): self
```

Multiplies this matrix by another one (this matrix is the left factor). The result has the rows of this matrix and the columns of the other.

- **Parameters:** `$other` a matrix with as many rows as this matrix has columns
- **Returns:** `Matrix`
- **Throws:** [DimensionMismatchException](errors.md) if the column count of this matrix differs from the row count of the other

## Shape and properties

Transpose, trace and the checks for squareness, singularity and finite components.

### transpose()

```php
public function transpose(): self
```

Swaps rows and columns. An m by n matrix becomes an n by m matrix.

- **Returns:** `Matrix`

### trace()

```php
public function trace(): float
```

The sum of the main diagonal.

- **Returns:** `float`
- **Throws:** [NotSquareException](errors.md) if the matrix is not square

### isSquare()

```php
public function isSquare(): bool
```

Tells whether the matrix has as many rows as columns.

- **Returns:** `bool`

### isFinite()

```php
public function isFinite(): bool
```

Tells whether all components are finite. Operations do not check their results for overflow, so a result can contain `INF` or `NAN`. Use this method to check a result when that matters.

- **Returns:** `bool`

## Determinant and inverse

Calculations that need a square matrix.

### determinant()

```php
public function determinant(): float
```

Calculates the determinant by Gaussian elimination with partial pivoting. A pivot at or below `1e-12` times the largest absolute component counts as zero, the same rule as `inverse()` and `isSingular()`. A singular matrix therefore gives exactly `0.0`.

- **Returns:** `float`
- **Throws:** [NotSquareException](errors.md) if the matrix is not square

### inverse()

```php
public function inverse(): self
```

Calculates the inverse by Gauss-Jordan elimination. A pivot at or below `1e-12` times the largest absolute component counts as zero.

- **Returns:** `Matrix`
- **Throws:** [NotSquareException](errors.md) if the matrix is not square
- **Throws:** [SingularMatrixException](errors.md) if the matrix has no inverse

### isSingular()

```php
public function isSingular(): bool
```

Tells whether the matrix has no inverse. It uses the same rule as `determinant()` and `inverse()`: a pivot at or below `1e-12` times the largest absolute component counts as zero.

- **Returns:** `bool`
- **Throws:** [NotSquareException](errors.md) if the matrix is not square

## Transformations

Build 4x4 transformation matrices and apply matrices to vectors. Angles are in radians and the matrices act on column vectors in a right-handed coordinate system.

### transform()

```php
public function transform(Vector $vector): Vector
```

Multiplies the matrix by a column vector. The result is a `Vector2`, `Vector3` or `Vector4` when it has two, three or four components, and a plain `Vector` otherwise.

- **Parameters:** `$vector` a vector with as many components as the matrix has columns
- **Returns:** `Vector`
- **Throws:** [DimensionMismatchException](errors.md) if the vector size differs from the column count, `PHPMath\Vector\Exception\InvalidComponentException` if a component of the result overflows to `INF`, because vectors never hold non-finite components

### translation()

```php
public static function translation(float $x, float $y, float $z): self
```

Returns a 4x4 matrix that moves points by the given distances. Directions (w = 0) are not moved.

- **Parameters:** `$x`, `$y` and `$z` the distances along each axis
- **Returns:** `Matrix`
- **Throws:** [InvalidTransformationException](errors.md) if a distance is `NAN` or infinite

### scaling()

```php
public static function scaling(float $x, float $y, float $z): self
```

Returns a 4x4 matrix that scales along the three axes.

- **Parameters:** `$x`, `$y` and `$z` the factors for each axis
- **Returns:** `Matrix`
- **Throws:** [InvalidTransformationException](errors.md) if a factor is `NAN` or infinite

### rotationX()

```php
public static function rotationX(float $angle): self
```

Returns a 4x4 matrix that rotates around the x axis. A positive angle turns the y axis towards the z axis.

- **Parameters:** `$angle` the angle in radians
- **Returns:** `Matrix`
- **Throws:** [InvalidTransformationException](errors.md) if the angle is `NAN` or infinite

### rotationY()

```php
public static function rotationY(float $angle): self
```

Returns a 4x4 matrix that rotates around the y axis. A positive angle turns the z axis towards the x axis.

- **Parameters:** `$angle` the angle in radians
- **Returns:** `Matrix`
- **Throws:** [InvalidTransformationException](errors.md) if the angle is `NAN` or infinite

### rotationZ()

```php
public static function rotationZ(float $angle): self
```

Returns a 4x4 matrix that rotates around the z axis. A positive angle turns the x axis towards the y axis.

- **Parameters:** `$angle` the angle in radians
- **Returns:** `Matrix`
- **Throws:** [InvalidTransformationException](errors.md) if the angle is `NAN` or infinite

### rotation()

```php
public static function rotation(Vector3 $axis, float $angle): self
```

Returns a 4x4 matrix that rotates around any axis through the origin. The axis does not need to be a unit vector.

- **Parameters:** `$axis` the rotation axis; `$angle` the angle in radians
- **Returns:** `Matrix`
- **Throws:** [InvalidTransformationException](errors.md) if the axis or the angle contains `NAN` or an infinite value
- **Throws:** [ZeroRotationAxisException](errors.md) if the axis is the zero vector

## Comparison

Compare two matrices.

### equals()

```php
public function equals(Matrix $other, float $tolerance = 0.0): bool
```

Returns `true` when both matrices have the same size and every pair of components differs by at most the tolerance. Matrices of different sizes return `false`.

- **Parameters:** `$other` the matrix to compare with; `$tolerance` the largest allowed absolute difference per component, default `0.0`
- **Returns:** `bool`
- **Throws:** [InvalidToleranceException](errors.md) if the tolerance is negative or `NAN`
