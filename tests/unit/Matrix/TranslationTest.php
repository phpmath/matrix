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
use const NAN;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, 'translation')]
final class TranslationTest extends TestCase
{
    public function testHasTheDistancesInTheLastColumn(): void
    {
        // Arrange

        // Act
        $result = Matrix::translation(1, 2, 3);

        // Assert
        self::assertSame(
            [[1.0, 0.0, 0.0, 1.0], [0.0, 1.0, 0.0, 2.0], [0.0, 0.0, 1.0, 3.0], [0.0, 0.0, 0.0, 1.0]],
            $result->toArray(),
        );
    }

    public function testMovesAPoint(): void
    {
        // Arrange
        $matrix = Matrix::translation(1, 2, 3);
        $point = new Vector4(1, 1, 1, 1);

        // Act
        $result = $matrix->transform($point);

        // Assert
        self::assertSame([2.0, 3.0, 4.0, 1.0], $result->toArray());
    }

    public function testDoesNotMoveADirection(): void
    {
        // Arrange
        $matrix = Matrix::translation(1, 2, 3);
        $direction = new Vector4(1, 1, 1, 0);

        // Act
        $result = $matrix->transform($direction);

        // Assert
        self::assertSame([1.0, 1.0, 1.0, 0.0], $result->toArray());
    }

    public function testRejectsAnInfiniteDistance(): void
    {
        // Arrange

        // Assert
        $this->expectException(InvalidTransformationException::class);
        $this->expectExceptionMessage('Transformation parameters must be finite numbers.');

        // Act
        Matrix::translation(INF, 0, 0);
    }

    public function testRejectsANanDistance(): void
    {
        // Arrange

        // Assert
        $this->expectException(InvalidTransformationException::class);

        // Act
        Matrix::translation(0, NAN, 0);
    }
}
