<?php
declare(strict_types = 1);

namespace EventSourcing;

class Memento
{
    private $state;

    public function __construct(string $state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}