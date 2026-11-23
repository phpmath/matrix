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
 * Thrown when a matrix has no inverse because it is singular.
 */
final class SingularMatrixException extends DomainException implements MatrixException
{
}
