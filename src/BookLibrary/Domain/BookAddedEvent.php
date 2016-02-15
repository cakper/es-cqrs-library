<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Calendar;
use EventSourcing\Event;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BookAddedEvent implements Event
{
    private $bookCopyId;
    private $addedOn;
    private $title;

    public function __construct(DateTimeImmutable $addedOn, UuidInterface $bookCopyId, string $title)
    {
        $this->bookCopyId = $bookCopyId;
        $this->addedOn = $addedOn;
        $this->title = $title;
    }

    public function getAddedOn() : DateTimeImmutable
    {
        return $this->addedOn;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->bookCopyId,
            'added_on' => $this->addedOn->getTimestamp(),
            'title'    => $this->title
        ];
    }

    public static function fromArray(array $data): Event
    {
        return new static(Calendar::getCurrentDateTime()->setTimestamp($data['added_on']), Uuid::fromString($data['id']), $data['title']);
    }
}