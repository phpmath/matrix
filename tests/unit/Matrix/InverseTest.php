<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\NotSquareException;
use PHPMath\Matrix\Exception\SingularMatrixException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'inverse')]
final class InverseTest extends TestCase
{
    public function testInvertsMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[4, 7], [2, 6]]);
        $expected = Matrix::fromRows([[0.6, -0.7], [-0.2, 0.4]]);

        // Act
        $inverse = $matrix->inverse();

        // Assert
        self::assertTrue($inverse->equals($expected, 1e-12));
    }

    public function testInverseTimesMatrixIsIdentity(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[2, 0, 1], [1, 3, 2], [1, 1, 2]]);

        // Act
        $product = $matrix->multiply($matrix->inverse());

        // Assert
        self::assertTrue($product->equals(Matrix::identity(3), 1e-12));
    }

    public function testInvertsMatrixNeedingRowSwap(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[0, 1], [1, 0]]);

        // Act
        $inverse = $matrix->inverse();

        // Assert
        self::assertTrue($inverse->equals($matrix));
    }

    public function testThrowsOnSingularMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [2, 4]]);
        $this->expectException(SingularMatrixException::class);

        // Act
        $matrix->inverse();
    }

    public function testThrowsOnZeroMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 2);
        $this->expectException(SingularMatrixException::class);

        // Act
        $matrix->inverse();
    }

    public function testThrowsOnNonSquareMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);
        $this->expectException(NotSquareException::class);

        // Act
        $matrix->inverse();
    }

    public function testThrowsOnRoundingSingularMatrixLikeDeterminant(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);
        $this->expectException(SingularMatrixException::class);

        // Act
        $matrix->inverse();
    }
}
