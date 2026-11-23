<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\NotSquareException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'determinant')]
final class DeterminantTest extends TestCase
{
    public function testCalculatesDeterminantOfOneByOne(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[7]]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertSame(7.0, $determinant);
    }

    public function testCalculatesDeterminantOfTwoByTwo(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [3, 4]]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertEqualsWithDelta(-2.0, $determinant, 1e-12);
    }

    public function testCalculatesDeterminantOfFourByFour(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([
            [1, 0, 2, -1],
            [3, 0, 0, 5],
            [2, 1, 4, -3],
            [1, 0, 5, 0],
        ]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertEqualsWithDelta(30.0, $determinant, 1e-9);
    }

    public function testHandlesRowSwapsWithPivoting(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[0, 1], [1, 0]]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertSame(-1.0, $determinant);
    }

    public function testReturnsZeroForSingularMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [2, 4]]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertSame(0.0, $determinant);
    }

    public function testThrowsOnNonSquareMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);
        $this->expectException(NotSquareException::class);

        // Act
        $matrix->determinant();
    }

    public function testReturnsExactZeroForRoundingSingularMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertSame(0.0, $determinant);
    }

    public function testReturnsExactZeroForZeroMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(3, 3);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertSame(0.0, $determinant);
    }

    public function testDoesNotTreatSmallInvertibleMatrixAsSingular(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E-100, 0], [0, 1.0E-100]]);

        // Act
        $determinant = $matrix->determinant();

        // Assert
        self::assertSame(1.0E-100 * 1.0E-100, $determinant);
    }
}
