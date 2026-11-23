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
#[CoversMethod(Matrix::class, 'columns')]
final class ColumnsTest extends TestCase
{
    public function testReturnsNumberOfColumns(): void
    {
        // Arrange
        $matrix = Matrix::zeros(2, 3);

        // Act
        $columns = $matrix->columns();

        // Assert
        self::assertSame(3, $columns);
    }
}
