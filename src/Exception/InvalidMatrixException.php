<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Exception;

use InvalidArgumentException;

/**
 * Thrown when matrix input is invalid: no rows or columns, ragged rows, components that are not finite ints or floats
 * or a size below one.
 */
final class InvalidMatrixException extends InvalidArgumentException implements MatrixException
{
}
