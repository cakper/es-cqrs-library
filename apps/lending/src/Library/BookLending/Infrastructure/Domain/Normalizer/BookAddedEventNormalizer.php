<?php
declare(strict_types = 1);
namespace Library\BookLending\Infrastructure\Domain\Normalizer;

use EventSourcing\Calendar;
use EventSourcing\Normalizer\Normalizer;
use Library\BookLending\Domain\BookAddedEvent;
use Ramsey\Uuid\Uuid;

class BookAddedEventNormalizer extends Normalizer
{
    function getSupportedClass() : string
    {
        return BookAddedEvent::class;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'id' => (string)$object->getBookCopyId(),
            'added_on' => $object->getAddedOn()->getTimestamp(),
            'title' => $object->getTitle()
        ];
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return new BookAddedEvent(
            Calendar::getCurrentDateTime()->setTimestamp($data['added_on']),
            Uuid::fromString($data['id']),
            $data['title']
        );
    }
}