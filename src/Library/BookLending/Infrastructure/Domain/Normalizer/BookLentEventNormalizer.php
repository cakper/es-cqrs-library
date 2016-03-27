<?php
declare(strict_types = 1);
namespace Library\BookLending\Infrastructure\Domain\Normalizer;

use EventSourcing\Calendar;
use EventSourcing\Normalizer\Normalizer;
use Library\BookLending\Domain\BookLentEvent;
use Ramsey\Uuid\Uuid;

class BookLentEventNormalizer extends Normalizer
{
    function getSupportedClass() : string
    {
        return BookLentEvent::class;
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