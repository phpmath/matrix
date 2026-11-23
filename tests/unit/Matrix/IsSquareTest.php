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
#[CoversMethod(Matrix::class, 'isSquare')]
final class IsSquareTest extends TestCase
{
    public function testReturnsTrueForSquareMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 2);

        // Act
        $result = $matrix->isSquare();

        // Assert
        self::assertTrue($result);
    }

    public function testReturnsFalseForRectangularMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);

        // Act
        $result = $matrix->isSquare();

        // Assert
        self::assertFalse($result);
    }
}
