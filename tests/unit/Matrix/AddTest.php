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
#[CoversMethod(Matrix::class, 'add')]
final class AddTest extends TestCase
{
    public function testAddsComponents(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2], [3, 4]]);
        $b = Matrix::fromRows([[5, 6], [7, 8]]);

        // Act
        $result = $a->add($b);

        // Assert
        self::assertSame([[6.0, 8.0], [10.0, 12.0]], $result->toArray());
    }

    public function testDoesNotChangeOriginal(): void
    {
        // Arrange
        $a = Matrix::fromRows([[1, 2]]);
        $b = Matrix::fromRows([[3, 4]]);

        // Act
        $a->add($b);

        // Assert
        self::assertSame([[1.0, 2.0]], $a->toArray());
    }

    public function testThrowsOnDifferentSize(): void
    {
        // Arrange
        $a = Matrix::zeros(2, 2);
        $this->expectException(DimensionMismatchException::class);

        // Act
        $a->add(Matrix::zeros(2, 3));
    }
}
