<?php
declare(strict_types = 1);
namespace Library\Infrastructure\Domain\Normalizer;

use Library\Domain\BookExtendedEvent;
use EventSourcing\Calendar;
use Infrastructure\Domain\Type;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BookExtendedEventNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BookExtendedEvent;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Type::forEventClass(BookExtendedEvent::class);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => $object->getBookCopyId()->toString(),
            'reader_id' => $object->getReaderId()->toString(),
            'extended_on' => $object->getExtendedOn()->getTimestamp(),
            'new_due_on' => $object->getNewDueDate()->getTimestamp(),
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new BookExtendedEvent(
            Uuid::fromString($data['id']),
            Uuid::fromString($data['reader_id']),
            Calendar::getCurrentDateTime()->setTimestamp($data['extended_on']),
            Calendar::getCurrentDateTime()->setTimestamp($data['new_due_on'])
        );
    }
}