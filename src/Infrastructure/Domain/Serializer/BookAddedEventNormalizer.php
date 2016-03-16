<?php
declare(strict_types = 1);
namespace Infrastructure\Domain\Serializer;

use BookLibrary\Domain\BookAddedEvent;
use EventSourcing\Calendar;
use Infrastructure\Domain\Type;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BookAddedEventNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BookAddedEvent;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Type::forEventClass(BookAddedEvent::class);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var $object BookAddedEvent */
        return [
            'id'       => (string)$object->getBookCopyId(),
            'added_on' => $object->getAddedOn()->getTimestamp(),
            'title'    => $object->getTitle()
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