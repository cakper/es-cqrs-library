<?php

namespace EventSourcing;

interface Projection {
    public function flush();
}