<?php
declare(strict_types = 1);
namespace AppBundle\Command;

use BookLibrary\Application\AddBookCommand as DomainAddBookCommand;
use BookLibrary\Domain\BookAddedEvent;
use EventSourcing\Calendar;
use Infrastructure\Domain\Serializer\BookAddedEventNormalizer;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class UuidNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return (string)$object;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof UuidInterface;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return Uuid::fromString($data);
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        var_dump($data);
        var_dump($type);
        die;
        return is_a($type, UuidInterface::class, true);
    }
}

class TestSerializerConsoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('test:serializer');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serializer = new Serializer([new BookAddedEventNormalizer()], [new JsonEncoder()]);

        $event = new BookAddedEvent(Calendar::getCurrentDateTime(), Uuid::uuid4(), 'Test');

        $data = $serializer->serialize($event, 'json');
//        var_dump($data);
        $obj = $serializer->deserialize($data, BookAddedEvent::class,'json');
//        var_dump($obj);
//        var_dump($event == $obj);
//        $bookId = Uuid::uuid4();
//        $command = new DomainAddBookCommand($bookId, 'abc');

//        $serialized = $serializer->serialize($command, 'json');
//        var_dump($serialized);

//        var_dump($serializer->deserialize($serialized, DomainAddBookCommand::class, 'json'));
    }
}