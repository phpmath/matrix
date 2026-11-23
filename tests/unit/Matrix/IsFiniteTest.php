<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'isFinite')]
final class IsFiniteTest extends TestCase
{
    public function testReturnsTrueForFiniteMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [3, 4]]);

        // Act
        $result = $matrix->isFinite();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseAfterOverflowInScale(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E+308, 1]])->scale(10);

        // Act
        $result = $matrix->isFinite();

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseAfterOverflowInAdd(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E+308]])->add(Matrix::fromRows([[1.0E+308]]));

        // Act
        $result = $matrix->isFinite();

        // Assert
        self::assertFalse($result);
    }

    public function testReturnsFalseForNan(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E+308, 0]])->scale(10)->scale(0);

        // Act
        $result = $matrix->isFinite();

        // Assert
        self::assertFalse($result);
    }
}
