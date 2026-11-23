<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\DimensionMismatchException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'multiply')]
final class MultiplyTest extends TestCase
{
    public function testMultipliesSquareMatrices(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2], [3, 4]]);
        $b = Matrix::fromRows([[5, 6], [7, 8]]);

        // Act
        $result = $a->multiply($b);

        // Assert
        self::assertSame([[19.0, 22.0], [43.0, 50.0]], $result->toArray());
    }

    public function testMultipliesRectangularMatrices(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2, 3]]);
        $b = Matrix::fromRows([[4], [5], [6]]);

        // Act
        $result = $a->multiply($b);

        // Assert
        self::assertSame([[32.0]], $result->toArray());
    }

    public function testMultiplyingByIdentityKeepsMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [3, 4]]);

        // Act
        $result = $matrix->multiply(Matrix::identity(2));

        // Assert
        self::assertTrue($result->equals($matrix));
    }

    public function testThrowsOnIncompatibleDimensions(): void
    {
        // Arrange
        $a = Matrix::zeros(2, 3);
        $this->expectException(DimensionMismatchException::class);

        // Act
        $a->multiply(Matrix::zeros(2, 3));
    }
}
