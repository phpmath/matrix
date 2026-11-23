<?php

/**
 * phpmath / matrix (https://github.com/phpmath/matrix)
 *
 * @link https://github.com/phpmath/matrix for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Matrix\Exception;

use DomainException;

/**
 * Thrown when a rotation axis is the zero vector, so no rotation is defined.
 */
final class ZeroRotationAxisException extends DomainException implements MatrixException
{
}
