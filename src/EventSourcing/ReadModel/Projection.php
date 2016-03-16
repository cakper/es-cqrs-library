<?php

namespace EventSourcing\ReadModel;

interface Projection {
    public function flush();
}