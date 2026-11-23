<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\InvalidMatrixException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const NAN;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'fromRows')]
final class FromRowsTest extends TestCase
{
    public function testCreatesMatrixFromRows(): void
    {
        // Arrange
        $rows = [[1, 2], [3, 4]];

        // Act
        $matrix = Matrix::fromRows($rows);

        // Assert
        self::assertSame([[1.0, 2.0], [3.0, 4.0]], $matrix->toArray());
    }

    public function testReindexesRows(): void
    {
        // Arrange
        $rows = ['a' => ['x' => 1, 'y' => 2]];

        // Act
        $matrix = Matrix::fromRows($rows);

        // Assert
        self::assertSame([[1.0, 2.0]], $matrix->toArray());
    }

    public function testThrowsOnNoRows(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::fromRows([]);
    }

    public function testThrowsOnEmptyRow(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::fromRows([[]]);
    }

    public function testThrowsOnRaggedRows(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::fromRows([[1, 2], [3]]);
    }

    public function testThrowsOnNonFiniteComponent(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::fromRows([[1.0, NAN]]);
    }

    public function testThrowsOnNumericString(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);
        $this->expectExceptionMessage('Matrix components must be of type int or float.');

        // Act
        Matrix::fromRows([[1, '2']]);
    }

    public function testThrowsOnNull(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::fromRows([[1, null]]);
    }

    public function testThrowsOnBoolean(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::fromRows([[true]]);
    }
}
