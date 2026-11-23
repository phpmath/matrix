<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Exception;

use OutOfRangeException;

/**
 * Thrown when a row or column index does not exist in the matrix.
 */
final class IndexOutOfRangeException extends OutOfRangeException implements MatrixException
{
}
