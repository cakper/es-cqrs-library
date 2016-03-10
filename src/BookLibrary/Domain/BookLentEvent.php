<?php
declare(strict_types = 1);
namespace BookLibrary\Domain;

use DateTimeImmutable;
use EventSourcing\Calendar;
use EventSourcing\Event;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class BookLentEvent implements Event
{
    private $bookCopyId;
    private $readerId;
    private $lentOn;
    private $dueOn;

    public function __construct(UuidInterface $bookCopyId, UuidInterface $readerId, DateTimeImmutable $lentOn, DateTimeImmutable $dueOn)
    {
        $this->bookCopyId = $bookCopyId;
        $this->readerId = $readerId;
        $this->lentOn = $lentOn;
        $this->dueOn = $dueOn;
    }

    public function getAggregateId() : UuidInterface
    {
        return $this->bookCopyId;
    }

    public function getDueOn() : DateTimeImmutable
    {
        return $this->dueOn;
    }

    public function getLentOn() : DateTimeImmutable
    {
        return $this->lentOn;
    }

    public function getReaderId() : UuidInterface
    {
        return $this->readerId;
    }

    public function toArray() : array
    {
        return [
            'id'        => $this->bookCopyId,
            'reader_id' => $this->readerId,
            'lent_on'   => $this->lentOn->getTimestamp(),
            'due_on'    => $this->dueOn->getTimestamp(),
        ];
    }

    public static function fromArray(array $data): Event
    {
        return new static(Uuid::fromString($data['id']), Uuid::fromString($data['reader_id']), Calendar::getCurrentDateTime()->setTimestamp($data['lent_on']), Calendar::getCurrentDateTime()->setTimestamp($data['due_on']));
    }
}