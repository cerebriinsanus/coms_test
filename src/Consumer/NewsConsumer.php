<?php
namespace App\Consumer;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\SerializerInterface;

class NewsConsumer implements ConsumerInterface
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * @var AMQPMessage $msg
     * @return void
     */
    public function execute(AMQPMessage $msg)
    {
        $entity = $this->serializer->deserialize($msg->getBody(),News::class,"json");
        $this->em->persist($entity);
        $this->em->flush();
    }
}