<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix;

use PHPMath\Matrix\Exception\DimensionMismatchException;
use PHPMath\Matrix\Exception\IndexOutOfRangeException;
use PHPMath\Matrix\Exception\InvalidMatrixException;
use PHPMath\Matrix\Exception\InvalidToleranceException;
use PHPMath\Matrix\Exception\InvalidTransformationException;
use PHPMath\Matrix\Exception\NotSquareException;
use PHPMath\Matrix\Exception\SingularMatrixException;
use PHPMath\Matrix\Exception\ZeroRotationAxisException;
use PHPMath\Vector\Vector;
use PHPMath\Vector\Vector2;
use PHPMath\Vector\Vector3;
use PHPMath\Vector\Vector4;
use Stringable;

use function abs;
use function array_fill;
use function array_map;
use function array_values;
use function cos;
use function count;
use function implode;
use function is_finite;
use function is_float;
use function is_int;
use function is_nan;
use function max;
use function rtrim;
use function sin;
use function sprintf;

/**
 * An immutable matrix of float components. Operations never change the instance they
 * are called on, they return a new instance.
 */
final readonly class Matrix implements Stringable
{
    /**
     * The relative threshold: a pivot at or below this fraction of the largest absolute component counts as zero.
     */
    private const float SINGULARITY_THRESHOLD = 1e-12;

    /**
     * The maximum number of decimals written by __toString().
     */
    private const int STRING_DECIMALS = 14;

    /** @var list<list<float>> */
    private array $data;

    /** @param array<int, array<int, float>> $data The validated, non-empty and rectangular rows. */
    private function __construct(array $data)
    {
        $this->data = array_values(array_map(array_values(...), $data));
    }

    /**
     * @param array<int|string, array<int|string, mixed>> $rows The rows of the matrix, holding int or float values.
     *
     * @throws InvalidMatrixException Thrown when the rows are empty, ragged or hold values that are not finite
     *                                numbers of type int or float.
     */
    public static function fromRows(array $rows): self
    {
        if ($rows === []) {
            throw new InvalidMatrixException('A matrix needs at least one row.');
        }

        $data = [];
        $columns = null;

        foreach ($rows as $row) {
            $values = array_values($row);
            $columns ??= count($values);

            if ($values === []) {
                throw new InvalidMatrixException('A matrix needs at least one column.');
            }

            if (count($values) !== $columns) {
                throw new InvalidMatrixException('All rows of a matrix must have the same number of columns.');
            }

            $floats = [];

            foreach ($values as $value) {
                if (! is_int($value) && ! is_float($value)) {
                    throw new InvalidMatrixException('Matrix components must be of type int or float.');
                }

                if (! is_finite($value)) {
                    throw new InvalidMatrixException('Matrix components must be finite numbers.');
                }

                $floats[] = (float) $value;
            }

            $data[] = $floats;
        }

        return new self($data);
    }

    /** @throws InvalidMatrixException Thrown when the size is smaller than one. */
    public static function identity(int $size): self
    {
        $matrix = self::zeros($size, $size)->data;

        for ($i = 0; $i < $size; ++$i) {
            $matrix[$i][$i] = 1.0;
        }

        return new self($matrix);
    }

    /** @throws InvalidMatrixException Thrown when the rows or columns are smaller than one. */
    public static function zeros(int $rows, int $columns): self
    {
        if ($rows < 1 || $columns < 1) {
            throw new InvalidMatrixException('A matrix needs at least one row and one column.');
        }

        return new self(array_fill(0, $rows, array_fill(0, $columns, 0.0)));
    }

    /**
     * A 4x4 matrix that moves points by the given distances. Directions (w = 0) are not moved.
     *
     * Matrices act on column vectors, so combine transformations right to left: $a->multiply($b) applies $b first.
     *
     * @throws InvalidTransformationException Thrown when a distance is not finite.
     */
    public static function translation(float $x, float $y, float $z): self
    {
        self::assertFinite($x, $y, $z);

        return new self([
            [1.0, 0.0, 0.0, $x + 0.0],
            [0.0, 1.0, 0.0, $y + 0.0],
            [0.0, 0.0, 1.0, $z + 0.0],
            [0.0, 0.0, 0.0, 1.0],
        ]);
    }

    /**
     * A 4x4 matrix that scales along the three axes.
     *
     * @throws InvalidTransformationException Thrown when a factor is not finite.
     */
    public static function scaling(float $x, float $y, float $z): self
    {
        self::assertFinite($x, $y, $z);

        return new self([
            [$x + 0.0, 0.0, 0.0, 0.0],
            [0.0, $y + 0.0, 0.0, 0.0],
            [0.0, 0.0, $z + 0.0, 0.0],
            [0.0, 0.0, 0.0, 1.0],
        ]);
    }

    /**
     * A 4x4 matrix that rotates around the x axis. The angle is in radians and a positive angle turns the y axis
     * towards the z axis (right-handed).
     *
     * @throws InvalidTransformationException Thrown when the angle is not finite.
     */
    public static function rotationX(float $angle): self
    {
        self::assertFinite($angle);

        $cos = cos($angle);
        $sin = sin($angle);

        return new self([
            [1.0, 0.0, 0.0, 0.0],
            [0.0, $cos, -$sin + 0.0, 0.0],
            [0.0, $sin, $cos, 0.0],
            [0.0, 0.0, 0.0, 1.0],
        ]);
    }

    /**
     * A 4x4 matrix that rotates around the y axis. The angle is in radians and a positive angle turns the z axis
     * towards the x axis (right-handed).
     *
     * @throws InvalidTransformationException Thrown when the angle is not finite.
     */
    public static function rotationY(float $angle): self
    {
        self::assertFinite($angle);

        $cos = cos($angle);
        $sin = sin($angle);

        return new self([
            [$cos, 0.0, $sin, 0.0],
            [0.0, 1.0, 0.0, 0.0],
            [-$sin + 0.0, 0.0, $cos, 0.0],
            [0.0, 0.0, 0.0, 1.0],
        ]);
    }

    /**
     * A 4x4 matrix that rotates around the z axis. The angle is in radians and a positive angle turns the x axis
     * towards the y axis (right-handed).
     *
     * @throws InvalidTransformationException Thrown when the angle is not finite.
     */
    public static function rotationZ(float $angle): self
    {
        self::assertFinite($angle);

        $cos = cos($angle);
        $sin = sin($angle);

        return new self([
            [$cos, -$sin + 0.0, 0.0, 0.0],
            [$sin, $cos, 0.0, 0.0],
            [0.0, 0.0, 1.0, 0.0],
            [0.0, 0.0, 0.0, 1.0],
        ]);
    }

    /**
     * A 4x4 matrix that rotates around any axis through the origin. The axis does not have to be a unit vector.
     * The angle is in radians and a positive angle turns counterclockwise when you look against the axis.
     *
     * @throws InvalidTransformationException Thrown when the axis or the angle is not finite.
     * @throws ZeroRotationAxisException Thrown when the axis is the zero vector.
     */
    public static function rotation(Vector3 $axis, float $angle): self
    {
        self::assertFinite($angle, ...$axis->toArray());

        $length = $axis->getLength();

        if ($length === 0.0) {
            throw new ZeroRotationAxisException('The rotation axis must not be the zero vector.');
        }

        $x = $axis->getX() / $length;
        $y = $axis->getY() / $length;
        $z = $axis->getZ() / $length;
        $cos = cos($angle);
        $sin = sin($angle);
        $t = 1.0 - $cos;

        return new self([
            [$t * $x * $x + $cos, $t * $x * $y - $sin * $z, $t * $x * $z + $sin * $y, 0.0],
            [$t * $x * $y + $sin * $z, $t * $y * $y + $cos, $t * $y * $z - $sin * $x, 0.0],
            [$t * $x * $z - $sin * $y, $t * $y * $z + $sin * $x, $t * $z * $z + $cos, 0.0],
            [0.0, 0.0, 0.0, 1.0],
        ]);
    }

    public function __toString(): string
    {
        $rows = array_map(
            static fn (array $row): string => '[' . implode(', ', array_map(self::formatNumber(...), $row)) . ']',
            $this->data,
        );

        return '[' . implode(', ', $rows) . ']';
    }

    /** @throws DimensionMismatchException Thrown when the matrices have a different size. */
    public function add(self $other): self
    {
        $this->assertSameSize($other);

        return $this->combine($other, static fn (float $a, float $b): float => $a + $b);
    }

    public function columns(): int
    {
        return count($this->data[0]);
    }

    /**
     * A pivot at or below 1e-12 times the largest absolute component counts as zero, so a singular matrix gives
     * exactly 0.0.
     *
     * @throws NotSquareException Thrown when the matrix is not square.
     */
    public function determinant(): float
    {
        return $this->eliminate() ?? 0.0;
    }

    /**
     * @param float $tolerance The maximum absolute difference allowed per component.
     *
     * @throws InvalidToleranceException Thrown when the tolerance is negative or not a number.
     */
    public function equals(self $other, float $tolerance = 0.0): bool
    {
        if (! ($tolerance >= 0.0)) {
            throw new InvalidToleranceException('The tolerance must be zero or greater.');
        }

        if ($this->rows() !== $other->rows() || $this->columns() !== $other->columns()) {
            return false;
        }

        foreach ($this->data as $r => $row) {
            foreach ($row as $c => $value) {
                if (abs($value - $other->data[$r][$c]) > $tolerance) {
                    return false;
                }
            }
        }

        return true;
    }

    /** @throws IndexOutOfRangeException Thrown when the row or column does not exist. */
    public function get(int $row, int $column): float
    {
        if ($row < 0 || $row >= $this->rows() || $column < 0 || $column >= $this->columns()) {
            throw new IndexOutOfRangeException(sprintf(
                'Position (%d, %d) is outside of a %dx%d matrix.',
                $row,
                $column,
                $this->rows(),
                $this->columns(),
            ));
        }

        return $this->data[$row][$column];
    }

    /**
     * @throws NotSquareException Thrown when the matrix is not square.
     * @throws SingularMatrixException Thrown when the matrix has no inverse.
     */
    public function inverse(): self
    {
        $this->assertSquare();

        $size = $this->rows();
        $work = $this->data;
        $inverse = self::identity($size)->data;
        $threshold = $this->singularityThreshold();

        for ($column = 0; $column < $size; ++$column) {
            $pivotRow = $this->findPivotRow($work, $column);
            $pivot = $work[$pivotRow][$column];

            if (abs($pivot) <= $threshold) {
                throw new SingularMatrixException('The matrix is singular and cannot be inverted.');
            }

            [$work[$pivotRow], $work[$column]] = [$work[$column], $work[$pivotRow]];
            [$inverse[$pivotRow], $inverse[$column]] = [$inverse[$column], $inverse[$pivotRow]];

            for ($k = 0; $k < $size; ++$k) {
                $work[$column][$k] /= $pivot;
                $inverse[$column][$k] /= $pivot;
            }

            for ($row = 0; $row < $size; ++$row) {
                if ($row === $column) {
                    continue;
                }

                $factor = $work[$row][$column];

                for ($k = 0; $k < $size; ++$k) {
                    $work[$row][$k] -= $factor * $work[$column][$k];
                    $inverse[$row][$k] -= $factor * $inverse[$column][$k];
                }
            }
        }

        return new self($inverse);
    }

    /**
     * Tells whether all components are finite. Results of operations are not checked for overflow, so they can
     * contain INF or NAN.
     */
    public function isFinite(): bool
    {
        foreach ($this->data as $row) {
            foreach ($row as $value) {
                if (! is_finite($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Tells whether the matrix is singular, using the same rule as determinant() and inverse().
     *
     * @throws NotSquareException Thrown when the matrix is not square.
     */
    public function isSingular(): bool
    {
        return $this->eliminate() === null;
    }

    public function isSquare(): bool
    {
        return $this->rows() === $this->columns();
    }

    /** @throws DimensionMismatchException Thrown when the column count of this matrix differs from the row count of the other. */
    public function multiply(self $other): self
    {
        if ($this->columns() !== $other->rows()) {
            throw new DimensionMismatchException(sprintf(
                'Cannot multiply a %dx%d matrix by a %dx%d matrix.',
                $this->rows(),
                $this->columns(),
                $other->rows(),
                $other->columns(),
            ));
        }

        $result = self::zeros($this->rows(), $other->columns())->data;

        foreach ($this->data as $r => $row) {
            foreach ($row as $k => $value) {
                foreach ($other->data[$k] as $c => $otherValue) {
                    $result[$r][$c] += $value * $otherValue;
                }
            }
        }

        return new self($result);
    }

    public function rows(): int
    {
        return count($this->data);
    }

    public function scale(float $factor): self
    {
        return new self(array_map(
            static fn (array $row): array => array_map(static fn (float $value): float => $value * $factor, $row),
            $this->data,
        ));
    }

    /** @throws DimensionMismatchException Thrown when the matrices have a different size. */
    public function subtract(self $other): self
    {
        $this->assertSameSize($other);

        return $this->combine($other, static fn (float $a, float $b): float => $a - $b);
    }

    /** @return list<list<float>> */
    public function toArray(): array
    {
        return $this->data;
    }

    /** @throws NotSquareException Thrown when the matrix is not square. */
    public function trace(): float
    {
        $this->assertSquare();

        $trace = 0.0;

        foreach ($this->data as $i => $row) {
            $trace += $row[$i];
        }

        return $trace;
    }

    /**
     * Multiplies this matrix by a column vector, which applies the transformation to the vector.
     *
     * The vector must have as many components as the matrix has columns. The result has as many components as the
     * matrix has rows, and is a Vector2, Vector3 or Vector4 when it has two, three or four components.
     *
     * @throws DimensionMismatchException Thrown when the vector size differs from the column count.
     */
    public function transform(Vector $vector): Vector
    {
        if ($this->columns() !== $vector->getSize()) {
            throw new DimensionMismatchException(sprintf(
                'Cannot transform a vector with %d components by a %dx%d matrix.',
                $vector->getSize(),
                $this->rows(),
                $this->columns(),
            ));
        }

        $components = [];

        foreach ($this->data as $row) {
            $sum = 0.0;

            foreach ($row as $column => $value) {
                $sum += $value * $vector->getElement($column);
            }

            $components[] = $sum;
        }

        return match (count($components)) {
            2 => new Vector2($components[0], $components[1]),
            3 => new Vector3($components[0], $components[1], $components[2]),
            4 => new Vector4($components[0], $components[1], $components[2], $components[3]),
            default => new Vector($components),
        };
    }

    public function transpose(): self
    {
        $result = self::zeros($this->columns(), $this->rows())->data;

        foreach ($this->data as $r => $row) {
            foreach ($row as $c => $value) {
                $result[$c][$r] = $value;
            }
        }

        return new self($result);
    }

    /**
     * Gaussian elimination with partial pivoting. Returns null when a pivot counts as zero.
     *
     * @throws NotSquareException
     */
    private function eliminate(): ?float
    {
        $this->assertSquare();

        $size = $this->rows();
        $lu = $this->data;
        $threshold = $this->singularityThreshold();
        $determinant = 1.0;

        for ($column = 0; $column < $size; ++$column) {
            $pivotRow = $this->findPivotRow($lu, $column);

            if (abs($lu[$pivotRow][$column]) <= $threshold) {
                return null;
            }

            if ($pivotRow !== $column) {
                [$lu[$pivotRow], $lu[$column]] = [$lu[$column], $lu[$pivotRow]];
                $determinant = -$determinant;
            }

            $determinant *= $lu[$column][$column];

            for ($row = $column + 1; $row < $size; ++$row) {
                $factor = $lu[$row][$column] / $lu[$column][$column];

                for ($k = $column; $k < $size; ++$k) {
                    $lu[$row][$k] -= $factor * $lu[$column][$k];
                }
            }
        }

        return $determinant;
    }

    /** The one rule for zero pivots: a fraction of the largest absolute component. */
    private function singularityThreshold(): float
    {
        $scale = 0.0;

        foreach ($this->data as $row) {
            foreach ($row as $value) {
                $scale = max($scale, abs($value));
            }
        }

        return self::SINGULARITY_THRESHOLD * $scale;
    }

    /** Plain decimals, at most STRING_DECIMALS of them, no exponent, no trailing zeros and no negative zero. */
    private static function formatNumber(float $value): string
    {
        if (! is_finite($value)) {
            return is_nan($value) ? 'NAN' : ($value > 0.0 ? 'INF' : '-INF');
        }

        $text = rtrim(rtrim(sprintf('%.' . self::STRING_DECIMALS . 'F', $value), '0'), '.');

        return $text === '-0' ? '0' : $text;
    }

    /** @param callable(float, float): float $operation */
    private function combine(self $other, callable $operation): self
    {
        $result = [];

        foreach ($this->data as $r => $row) {
            $result[] = array_map(
                static fn (float $a, float $b): float => $operation($a, $b),
                $row,
                $other->data[$r],
            );
        }

        return new self($result);
    }

    /** @param list<list<float>> $work */
    private function findPivotRow(array $work, int $column): int
    {
        $pivotRow = $column;

        for ($row = $column + 1; $row < count($work); ++$row) {
            if (abs($work[$row][$column]) <= abs($work[$pivotRow][$column])) {
                continue;
            }

            $pivotRow = $row;
        }

        return $pivotRow;
    }

    /** @throws InvalidTransformationException */
    private static function assertFinite(float ...$values): void
    {
        foreach ($values as $value) {
            if (! is_finite($value)) {
                throw new InvalidTransformationException('Transformation parameters must be finite numbers.');
            }
        }
    }

    /** @throws DimensionMismatchException */
    private function assertSameSize(self $other): void
    {
        if ($this->rows() !== $other->rows() || $this->columns() !== $other->columns()) {
            throw new DimensionMismatchException(sprintf(
                'Expected a %dx%d matrix, got a %dx%d matrix.',
                $this->rows(),
                $this->columns(),
                $other->rows(),
                $other->columns(),
            ));
        }
    }

    /** @throws NotSquareException */
    private function assertSquare(): void
    {
        if (! $this->isSquare()) {
            throw new NotSquareException(sprintf(
                'Expected a square matrix, got a %dx%d matrix.',
                $this->rows(),
                $this->columns(),
            ));
        }
    }
}
