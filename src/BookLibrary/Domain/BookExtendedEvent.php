<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\AggregateId;
use EventSourcing\Calendar;
use EventSourcing\Event;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BookExtendedEvent implements Event
{
    private $extendedOn;
    private $bookCopyId;
    private $newDueDate;
    /**
     * @var UuidInterface
     */
    private $readerId;

    public function __construct(UuidInterface $bookCopyId, UuidInterface $readerId, DateTimeImmutable $extendedOn, DateTimeImmutable $newDueDate)
    {
        $this->extendedOn = $extendedOn;
        $this->bookCopyId = $bookCopyId;
        $this->newDueDate = $newDueDate;
        $this->readerId = $readerId;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getExtendedOn() : DateTimeImmutable
    {
        return $this->extendedOn;
    }

    public function getNewDueDate() : DateTimeImmutable
    {
        return $this->newDueDate;
    }

    public function toArray() : array
    {
        return [
            'id'          => $this->bookCopyId,
            'reader_id'   => $this->readerId,
            'extended_on' => $this->extendedOn->getTimestamp(),
            'new_due_on'  => $this->newDueDate->getTimestamp(),
        ];
    }

    public static function fromArray(array $data): Event
    {
        return new static(Uuid::fromString($data['id']), Uuid::fromString($data['reader_id']), Calendar::getCurrentDateTime()->setTimestamp($data['extended_on']), Calendar::getCurrentDateTime()->setTimestamp($data['new_due_on']));
    }
}