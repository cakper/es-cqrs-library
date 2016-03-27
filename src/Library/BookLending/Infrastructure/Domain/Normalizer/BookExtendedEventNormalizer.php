<?php
declare(strict_types = 1);
namespace Library\BookLending\Infrastructure\Domain\Normalizer;

use EventSourcing\Calendar;
use EventSourcing\Normalizer\Normalizer;
use Library\BookLending\Domain\BookExtendedEvent;
use Ramsey\Uuid\Uuid;

class BookExtendedEventNormalizer extends Normalizer
{
    function getSupportedClass() : string
    {
        return BookExtendedEvent::class;
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