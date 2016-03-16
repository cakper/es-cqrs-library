<?php
declare(strict_types = 1);
namespace Infrastructure\Domain\Serializer;

use BookLibrary\Domain\BookLentEvent;
use EventSourcing\Calendar;
use Infrastructure\Domain\Type;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BookLentEventNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BookLentEvent;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Type::forEventClass(BookLentEvent::class);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getBookCopyId()->toString(),
            'reader_id' => $object->getReaderId()->toString(),
            'lent_on' => $object->getLentOn()->getTimestamp(),
            'due_on' => $object->getDueOn()->getTimestamp(),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new BookLentEvent(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['reader_id']),
            Calendar::getCurrentDateTime()->setTimestamp($data['lent_on']),
            Calendar::getCurrentDateTime()->setTimestamp($data['due_on'])
        );
    }
}