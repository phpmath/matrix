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

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'zeros')]
final class ZerosTest extends TestCase
{
    public function testCreatesMatrixFilledWithZeros(): void
    {
        // Act
        $matrix = Matrix::zeros(2, 3);

        // Assert
        self::assertSame([[0.0, 0.0, 0.0], [0.0, 0.0, 0.0]], $matrix->toArray());
    }

    public function testThrowsOnZeroRows(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::zeros(0, 2);
    }

    public function testThrowsOnZeroColumns(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::zeros(2, 0);
    }
}
