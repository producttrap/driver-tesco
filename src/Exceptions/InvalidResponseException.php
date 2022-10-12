<?php

declare(strict_types=1);

namespace ProductTrap\Tesco\Exceptions;

use ProductTrap\Contracts\ProductTrapException;

class InvalidResponseException extends \RuntimeException implements ProductTrapException
{
}
