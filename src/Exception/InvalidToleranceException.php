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
 * Thrown when a comparison tolerance is negative or not a number.
 */
final class InvalidToleranceException extends InvalidArgumentException implements MatrixException
{
}
