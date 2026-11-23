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
#[CoversMethod(Matrix::class, 'identity')]
final class IdentityTest extends TestCase
{
    public function testCreatesIdentityMatrix(): void
    {
        // Act
        $matrix = Matrix::identity(3);

        // Assert
        self::assertSame([[1.0, 0.0, 0.0], [0.0, 1.0, 0.0], [0.0, 0.0, 1.0]], $matrix->toArray());
    }

    public function testThrowsOnSizeSmallerThanOne(): void
    {
        // Arrange
        $this->expectException(InvalidMatrixException::class);

        // Act
        Matrix::identity(0);
    }
}
