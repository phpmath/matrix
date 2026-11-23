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
 * Thrown when an operation needs a square matrix and a non-square matrix is given.
 */
final class NotSquareException extends InvalidArgumentException implements MatrixException
{
}
