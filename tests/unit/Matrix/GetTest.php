<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\IndexOutOfRangeException;
use PHPMath\Matrix\Matrix;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'get')]
final class GetTest extends TestCase
{
    public function testReturnsComponent(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2], [3, 4]]);

        // Act
        $value = $matrix->get(1, 0);

        // Assert
        self::assertSame(3.0, $value);
    }

    public function testThrowsOnRowOutOfRange(): void
    {
        // Arrange
        $matrix = Matrix::identity(2);
        $this->expectException(IndexOutOfRangeException::class);

        // Act
        $matrix->get(2, 0);
    }

    public function testThrowsOnColumnOutOfRange(): void
    {
        // Arrange
        $matrix = Matrix::identity(2);
        $this->expectException(IndexOutOfRangeException::class);

        // Act
        $matrix->get(0, 2);
    }

    public function testThrowsOnNegativeIndex(): void
    {
        // Arrange
        $matrix = Matrix::identity(2);
        $this->expectException(IndexOutOfRangeException::class);

        // Act
        $matrix->get(-1, 0);
    }
}
