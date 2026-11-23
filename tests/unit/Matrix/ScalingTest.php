<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\InvalidTransformationException;
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector4;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'scaling')]
final class ScalingTest extends TestCase
{
    public function testHasTheFactorsOnTheDiagonal(): void
    {
        // Arrange

        // Act
        $result = Matrix::scaling(2, 3, 4);

        // Assert
        self::assertSame(
            [[2.0, 0.0, 0.0, 0.0], [0.0, 3.0, 0.0, 0.0], [0.0, 0.0, 4.0, 0.0], [0.0, 0.0, 0.0, 1.0]],
            $result->toArray(),
        );
    }

    public function testScalesAPoint(): void
    {
        // Arrange
        $matrix = Matrix::scaling(2, 3, 4);
        $point = new Vector4(1, 1, 1, 1);

        // Act
        $result = $matrix->transform($point);

        // Assert
        self::assertSame([2.0, 3.0, 4.0, 1.0], $result->toArray());
    }

    public function testRejectsAnInfiniteFactor(): void
    {
        // Arrange

        // Assert
        $this->expectException(InvalidTransformationException::class);
        $this->expectExceptionMessage('Transformation parameters must be finite numbers.');

        // Act
        Matrix::scaling(1, INF, 1);
    }
}
