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

use function ini_set;

use const PHP_FLOAT_MAX;

#[CoversClass(Matrix::class)]
#[CoversMethod(Matrix::class, '__toString')]
final class ToStringTest extends TestCase
{
    public function testFormatsRows(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1, 2.5], [3, 4]]);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[1, 2.5], [3, 4]]', $string);
    }

    public function testKeepsSmallNumbersAsPlainDecimals(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[0.0000001, 1.0E-14]]);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[0.0000001, 0.00000000000001]]', $string);
    }

    public function testNeverUsesExponent(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E+20, -1.0E-10]]);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[100000000000000000000, -0.0000000001]]', $string);
    }

    public function testRoundsToFourteenDecimals(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[0.123456789012345678, 1.0E-15]]);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[0.12345678901235, 0]]', $string);
    }

    public function testWritesNegativeZeroAsZero(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[-0.0, -1.0E-20]]);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[0, 0]]', $string);
    }

    public function testWritesNonFiniteResultsAsWords(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1.0E+308]])->scale(10);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[INF]]', $string);
    }

    public function testDoesNotDependOnPrecisionSetting(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[1 / 3, 0.0000001]]);
        $original = ini_set('precision', '3');

        // Act
        try {
            $string = (string) $matrix;
        } finally {
            ini_set('precision', (string) $original);
        }

        // Assert
        self::assertSame('[[0.33333333333333, 0.0000001]]', $string);
    }

    public function testPrintsNotANumberWithoutAWarning(): void
    {
        // Arrange
        $overflow = Matrix::fromRows([[PHP_FLOAT_MAX, 1]])->scale(10.0);
        $matrix = $overflow->subtract($overflow);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[NAN, 0]]', $string);
    }

    public function testPrintsNegativeInfinity(): void
    {
        // Arrange
        $matrix = Matrix::fromRows([[PHP_FLOAT_MAX]])->scale(-10.0);

        // Act
        $string = (string) $matrix;

        // Assert
        self::assertSame('[[-INF]]', $string);
    }
}
