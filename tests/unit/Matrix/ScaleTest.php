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
#[CoversMethod(Matrix::class, 'scale')]
final class ScaleTest extends TestCase
{
    public function testScalesComponents(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, -2], [3, 4]]);

        // Act
        $result = $matrix->scale(2.5);

        // Assert
        self::assertSame([[2.5, -5.0], [7.5, 10.0]], $result->toArray());
    }

    public function testDoesNotChangeOriginal(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2]]);

        // Act
        $matrix->scale(3.0);

        // Assert
        self::assertSame([[1.0, 2.0]], $matrix->toArray());
    }
}
