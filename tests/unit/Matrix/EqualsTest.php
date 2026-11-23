<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\InvalidToleranceException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'equals')]
final class EqualsTest extends TestCase
{
    public function testReturnsTrueForIdenticalMatrices(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2]]);

        // Act
        $result = $a->equals(Matrix::fromRows([[1, 2]]));

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForDifferentComponents(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2]]);

        // Act
        $result = $a->equals(Matrix::fromRows([[1, 2.1]]));

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsTrueWithinTolerance(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2]]);

        // Act
        $result = $a->equals(Matrix::fromRows([[1, 2.05]]), 0.1);

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseOutsideTolerance(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2]]);

        // Act
        $result = $a->equals(Matrix::fromRows([[1, 2.5]]), 0.1);

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForDifferentSize(): void
    {
        // Arrange
        $a = Matrix::zeros(2, 2);

        // Act
        $result = $a->equals(Matrix::zeros(2, 3));

        // Assert
        self::assertFalse($result);
    }

    public function testThrowsOnNegativeTolerance(): void
    {
        // Arrange
        $a = Matrix::zeros(1, 1);
        $this->expectException(InvalidToleranceException::class);

        // Act
        $a->equals($a, -0.1);
    }
}
