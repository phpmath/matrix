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
 * Thrown when a transformation distance, factor or angle is not finite.
 */
final class InvalidTransformationException extends InvalidArgumentException implements MatrixException
{
}
