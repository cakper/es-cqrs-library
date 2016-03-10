<?php
declare(strict_types = 1);
namespace Infrastructure\EventStore\Doctrine;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity()
 * @Table(name="book_available_view")
 */
class BookAvailableView
{
    /**
     * @Id
     * @Column(type="string", length=36, name="book_id", nullable=false)
     */
    public $bookId;

    /**
     * @Column(type="string", name="title", nullable=false)
     */
    public $title;

    /**
     * BookAvailableView constructor.
     *
     * @param $bookId
     * @param $title
     */
    public function __construct($bookId, $title)
    {
        $this->bookId = $bookId;
        $this->title = $title;
    }
}