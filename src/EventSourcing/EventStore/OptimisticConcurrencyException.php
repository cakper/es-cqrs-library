<?php
declare(strict_types = 1);
namespace EventSourcing\EventStore;

use RuntimeException;

class OptimisticConcurrencyException extends RuntimeException
{

}