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

use function sqrt;

use const INF;
use const M_PI;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'rotationZ')]
final class RotationZTest extends TestCase
{
    public function testTurnsAQuarterTurn(): void
    {
        // Arrange
        $matrix = Matrix::rotationZ(M_PI / 2);
        $point = new Vector4(1, 0, 0, 1);

        // Act
        $result = $matrix->transform($point);

        // Assert
        self::assertEqualsWithDelta([0.0, 1.0, 0.0, 1.0], $result->toArray(), 1.0E-12);
    }

    public function testZeroAngleChangesNothing(): void
    {
        // Arrange
        $matrix = Matrix::rotationZ(0);

        // Act
        $result = $matrix->toArray();

        // Assert
        self::assertSame(Matrix::identity(4)->toArray(), $result);
    }

    public function testKeepsTheLength(): void
    {
        // Arrange
        $matrix = Matrix::rotationZ(1.234);
        $point = new Vector4(1, 2, 3, 1);

        // Act
        $result = $matrix->transform($point);

        // Assert
        self::assertEqualsWithDelta(sqrt(14.0 + 1.0), $result->getLength(), 1.0E-12);
    }

    public function testRejectsAnInfiniteAngle(): void
    {
        // Arrange

        // Assert
        $this->expectException(InvalidTransformationException::class);
        $this->expectExceptionMessage('Transformation parameters must be finite numbers.');

        // Act
        Matrix::rotationZ(INF);
    }
}
