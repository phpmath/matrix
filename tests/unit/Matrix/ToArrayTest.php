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
#[CoversMethod(Matrix::class, 'toArray')]
final class ToArrayTest extends TestCase
{
    public function testReturnsRowsAsFloats(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2]]);

        // Act
        $array = $matrix->toArray();

        // Assert
        self::assertSame([[1.0, 2.0]], $array);
    }

    public function testChangingReturnedArrayDoesNotChangeMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2]]);
        $array = $matrix->toArray();
        $array[0][0] = 99.0;

        // Act
        $result = $matrix->toArray();

        // Assert
        self::assertSame([[1.0, 2.0]], $result);
    }
}
