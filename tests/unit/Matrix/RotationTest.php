<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\InvalidTransformationException;
use PHPMath\Matrix\Exception\ZeroRotationAxisException;
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Vector3;
use PHPMath\Vector\Vector4;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const M_PI;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'rotation')]
final class RotationTest extends TestCase
{
    public function testAroundTheZAxisEqualsRotationZ(): void
    {
        // Arrange
        $axis = new Vector3(0, 0, 1);

        // Act
        $result = Matrix::rotation($axis, 0.7);

        // Assert
        self::assertEqualsWithDelta(Matrix::rotationZ(0.7)->toArray(), $result->toArray(), 1.0E-12);
    }

    public function testAroundTheXAxisEqualsRotationX(): void
    {
        // Arrange
        $axis = new Vector3(1, 0, 0);

        // Act
        $result = Matrix::rotation($axis, 0.7);

        // Assert
        self::assertEqualsWithDelta(Matrix::rotationX(0.7)->toArray(), $result->toArray(), 1.0E-12);
    }

    public function testAroundTheYAxisEqualsRotationY(): void
    {
        // Arrange
        $axis = new Vector3(0, 1, 0);

        // Act
        $result = Matrix::rotation($axis, 0.7);

        // Assert
        self::assertEqualsWithDelta(Matrix::rotationY(0.7)->toArray(), $result->toArray(), 1.0E-12);
    }

    public function testAxisDoesNotHaveToBeAUnitVector(): void
    {
        // Arrange
        $axis = new Vector3(0, 0, 5);

        // Act
        $result = Matrix::rotation($axis, 0.7);

        // Assert
        self::assertEqualsWithDelta(Matrix::rotationZ(0.7)->toArray(), $result->toArray(), 1.0E-12);
    }

    public function testTurnsTheAxesIntoEachOtherAroundTheDiagonal(): void
    {
        // Arrange
        $matrix = Matrix::rotation(new Vector3(1, 1, 1), 2 * M_PI / 3);
        $point = new Vector4(1, 0, 0, 1);

        // Act
        $result = $matrix->transform($point);

        // Assert
        self::assertEqualsWithDelta([0.0, 1.0, 0.0, 1.0], $result->toArray(), 1.0E-12);
    }

    public function testKeepsThePointsOnTheAxisInPlace(): void
    {
        // Arrange
        $matrix = Matrix::rotation(new Vector3(1, 2, 2), 1.1);
        $point = new Vector4(1, 2, 2, 1);

        // Act
        $result = $matrix->transform($point);

        // Assert
        self::assertEqualsWithDelta([1.0, 2.0, 2.0, 1.0], $result->toArray(), 1.0E-12);
    }

    public function testRejectsTheZeroVectorAsAxis(): void
    {
        // Arrange
        $axis = new Vector3(0, 0, 0);

        // Assert
        $this->expectException(ZeroRotationAxisException::class);
        $this->expectExceptionMessage('The rotation axis must not be the zero vector.');

        // Act
        Matrix::rotation($axis, 1.0);
    }

    public function testRejectsAnInfiniteAngle(): void
    {
        // Arrange
        $axis = new Vector3(0, 0, 1);

        // Assert
        $this->expectException(InvalidTransformationException::class);
        $this->expectExceptionMessage('Transformation parameters must be finite numbers.');

        // Act
        Matrix::rotation($axis, INF);
    }

    public function testRejectsAnInfiniteAxis(): void
    {
        // Arrange
        $axis = (new Vector3(1.0E+308, 0.0, 1.0))->scale(10.0);

        // Assert
        $this->expectException(InvalidTransformationException::class);
        $this->expectExceptionMessage('Transformation parameters must be finite numbers.');

        // Act
        Matrix::rotation($axis, 1.0);
    }
}
