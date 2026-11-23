<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\NotSquareException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'trace')]
final class TraceTest extends TestCase
{
    public function testSumsDiagonal(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [3, 4]]);

        // Act
        $trace = $matrix->trace();

        // Assert
        self::assertSame(5.0, $trace);
    }

    public function testThrowsOnNonSquareMatrix(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);
        $this->expectException(NotSquareException::class);

        // Act
        $matrix->trace();
    }
}
