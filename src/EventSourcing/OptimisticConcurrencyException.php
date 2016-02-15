<?php
declare(strict_types = 1);
namespace EventSourcing;

use RuntimeException;

class OptimisticConcurrencyException extends RuntimeException
{

}