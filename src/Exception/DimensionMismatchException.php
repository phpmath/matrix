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
 * Thrown when the dimensions of matrices do not fit the requested operation.
 */
final class DimensionMismatchException extends InvalidArgumentException implements MatrixException
{
}
