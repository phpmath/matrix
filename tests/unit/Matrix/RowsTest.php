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
#[CoversMethod(Matrix::class, 'rows')]
final class RowsTest extends TestCase
{
    public function testReturnsNumberOfRows(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);

        // Act
        $rows = $matrix->rows();

        // Assert
        self::assertSame(2, $rows);
    }
}
