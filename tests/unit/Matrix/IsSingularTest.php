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
#[CoversMethod(Matrix::class, 'isSingular')]
final class IsSingularTest extends TestCase
{
    public function testReturnsTrueForRoundingSingularMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

        // Act
        $result = $matrix->isSingular();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsTrueForZeroMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 2);

        // Act
        $result = $matrix->isSingular();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForInvertibleMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[4, 7], [2, 6]]);

        // Act
        $result = $matrix->isSingular();

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForSmallButInvertibleMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E-200, 0], [0, 1.0E-200]]);

        // Act
        $result = $matrix->isSingular();

        // Assert
        self::assertFalse($result);
    }

    public function testThrowsOnNonSquareMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);
        $this->expectException(NotSquareException::class);

        // Act
        $matrix->isSingular();
    }
}
