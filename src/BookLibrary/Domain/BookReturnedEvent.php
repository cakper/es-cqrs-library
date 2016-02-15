<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Calendar;
use EventSourcing\Event;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BookReturnedEvent implements Event
{
    private $bookCopyId;
    private $readerId;
    private $returnedOn;
    private $dueOn;

    public function __construct(UuidInterface $bookCopyId, UuidInterface $readerId, DateTimeImmutable $returnedOn, DateTimeImmutable $dueOn)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->returnedOn = $returnedOn;
        $this->dueOn = $dueOn;
    }

    public function getDueOn() : DateTimeImmutable
    {
        return $this->dueOn;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }

    public function getReturnedOn() : DateTimeImmutable
    {
        return $this->returnedOn;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function toArray() : array
    {
        return [
            'id'          => $this->bookCopyId,
            'reader_id'   => $this->readerId,
            'returned_on' => $this->returnedOn->getTimestamp(),
            'due_on'      => $this->dueOn->getTimestamp(),
        ];
    }

    public static function fromArray(array $data): Event
    {
        return new static(Uuid::fromString($data['id']), Uuid::fromString($data['reader_id']), Calendar::getCurrentDateTime()->setTimestamp($data['returned_on']), Calendar::getCurrentDateTime()->setTimestamp($data['due_on']));
    }
}