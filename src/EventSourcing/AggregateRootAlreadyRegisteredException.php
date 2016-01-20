<?php
declare(strict_types = 1);
namespace EventSourcing;

use MongoDB\Driver\Exception\LogicException;

class AggregateRootAlreadyRegisteredException extends LogicException
{

}