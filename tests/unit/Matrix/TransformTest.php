<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Tests\Matrix;

use PHPMath\Matrix\Exception\DimensionMismatchException;
use PHPMath\Matrix\Matrix;
use PHPMath\Vector\Exception\InvalidComponentException;
use PHPMath\Vector\Vector;
use PHPMath\Vector\Vector2;
use PHPMath\Vector\Vector3;
use PHPMath\Vector\Vector4;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const M_PI;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'transform')]
final class TransformTest extends TestCase
{
    public function testReturnsAVector2ForATwoByTwoMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[0, -1], [1, 0]]);

        // Act
        $result = $matrix->transform(new Vector2(1, 0));

        // Assert
        self::assertInstanceOf(Vector2::class, $result);
        self::assertSame([0.0, 1.0], $result->toArray());
    }

    public function testReturnsAVector3ForAThreeByThreeMatrix(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 0, 0], [0, 2, 0], [0, 0, 3]]);

        // Act
        $result = $matrix->transform(new Vector3(1, 1, 1));

        // Assert
        self::assertInstanceOf(Vector3::class, $result);
        self::assertSame([1.0, 2.0, 3.0], $result->toArray());
    }

    public function testReturnsAVector4ForAFourByFourMatrix(): void
    {
        // Arrange
        $matrix = Matrix::identity(4);

        // Act
        $result = $matrix->transform(new Vector4(1, 2, 3, 4));

        // Assert
        self::assertInstanceOf(Vector4::class, $result);
        self::assertSame([1.0, 2.0, 3.0, 4.0], $result->toArray());
    }

    public function testUsesTheRowCountForTheSizeOfTheResult(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 0, 0], [0, 1, 0]]);

        // Act
        $result = $matrix->transform(new Vector3(7, 8, 9));

        // Assert
        self::assertInstanceOf(Vector2::class, $result);
        self::assertSame([7.0, 8.0], $result->toArray());
    }

    public function testReturnsAPlainVectorForOtherSizes(): void
    {
        // Arrange
        $matrix = Matrix::identity(5);

        // Act
        $result = $matrix->transform(new Vector([1, 2, 3, 4, 5]));

        // Assert
        self::assertSame(Vector::class, $result::class);
        self::assertSame([1.0, 2.0, 3.0, 4.0, 5.0], $result->toArray());
    }

    public function testReturnsAPlainVectorForASingleComponent(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[2]]);

        // Act
        $result = $matrix->transform(new Vector([4]));

        // Assert
        self::assertSame(Vector::class, $result::class);
        self::assertSame([8.0], $result->toArray());
    }

    public function testAppliesTheRightmostMatrixFirst(): void
    {
        // Arrange
        $translation = Matrix::translation(1, 0, 0);
        $rotation = Matrix::rotationZ(M_PI / 2);
        $combined = $rotation->multiply($translation);

        // Act
        $result = $combined->transform(new Vector4(0, 0, 0, 1));

        // Assert
        self::assertEqualsWithDelta([0.0, 1.0, 0.0, 1.0], $result->toArray(), 1.0E-12);
    }

    public function testInverseUndoesTheTransformation(): void
    {
        // Arrange
        $matrix = Matrix::rotationZ(0.5)->multiply(Matrix::translation(1, 2, 3));
        $point = new Vector4(4, 5, 6, 1);

        // Act
        $result = $matrix->inverse()->transform($matrix->transform($point));

        // Assert
        self::assertEqualsWithDelta([4.0, 5.0, 6.0, 1.0], $result->toArray(), 1.0E-9);
    }

    public function testRejectsAVectorOfTheWrongSize(): void
    {
        // Arrange
        $matrix = Matrix::identity(2);

        // Assert
        $this->expectException(DimensionMismatchException::class);
        $this->expectExceptionMessage('Cannot transform a vector with 3 components by a 2x2 matrix.');

        // Act
        $matrix->transform(new Vector3(1, 2, 3));
    }

    public function testThrowsWhenTheResultOverflows(): void
    {
        // Arrange
        $matrix = Matrix::scaling(1.0E+308, 1.0, 1.0);
        $vector = new Vector4(1.0E+10, 1.0, 1.0, 1.0);

        // Assert
        $this->expectException(InvalidComponentException::class);

        // Act
        $matrix->transform($vector);
    }
}
