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
#[CoversMethod(Matrix::class, 'transpose')]
final class TransposeTest extends TestCase
{
    public function testSwapsRowsAndColumns(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2, 3], [4, 5, 6]]);

        // Act
        $result = $matrix->transpose();

        // Assert
        self::assertSame([[1.0, 4.0], [2.0, 5.0], [3.0, 6.0]], $result->toArray());
    }
}
