<?php
declare(strict_types = 1);
namespace Infrastructure\Domain\Serializer;

use BookLibrary\Domain\BookReturnedEvent;
use EventSourcing\Calendar;
use Infrastructure\Domain\Type;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BookReturnedEventNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BookReturnedEvent;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Type::forEventClass(BookReturnedEvent::class);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var $object BookReturnedEvent */
        return [
            'id' => $object->getBookCopyId()->toString(),
            'reader_id' => $object->getReaderId()->toString(),
            'returned_on' => $object->getReturnedOn()->getTimestamp(),
            'due_on' => $object->getDueOn()->getTimestamp(),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new BookReturnedEvent(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['reader_id']),
            Calendar::getCurrentDateTime()->setTimestamp($data['returned_on']),
            Calendar::getCurrentDateTime()->setTimestamp($data['due_on'])
        );
    }
}