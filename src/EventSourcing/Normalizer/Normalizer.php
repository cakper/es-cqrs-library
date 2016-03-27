<?php

namespace EventSourcing\Normalizer;

use EventSourcing\EventStore\TypeMapping;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class Normalizer implements NormalizerInterface, DenormalizerInterface
{
    abstract function getSupportedClass() : string;

    private $typeMapping;

    public function __construct(TypeMapping $typeMapping)
    {
        $this->typeMapping = $typeMapping;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_a($data, $this->getSupportedClass(), true);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === $this->typeMapping->forEventClass($this->getSupportedClass());
    }
}