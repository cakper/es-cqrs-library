<?php
declare(strict_types = 1);
namespace Library\BookLending\Infrastructure\Domain\Normalizer;

use EventSourcing\Calendar;
use EventSourcing\Normalizer\Normalizer;
use Library\BookLending\Domain\BookReturnedEvent;
use Ramsey\Uuid\Uuid;

class BookReturnedEventNormalizer extends Normalizer
{
    function getSupportedClass() : string
    {
        return BookReturnedEvent::class;
    }

    public function normalize($object, $format = null, array $context = [])
    {
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