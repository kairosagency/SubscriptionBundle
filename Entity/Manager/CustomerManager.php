<?php
namespace Kairos\SubscriptionBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Kairos\SubscriptionBundle\Model\Customer;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class CustomerManager
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class, ContainerInterface $container)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;

        $this->container = $container;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $subscriptionId
     * @return array
     */
    public function findBySubscriptionCustomerId($subscriptionCustomerId)
    {
        return $this->repository->findBy(array('subscriptionCustomerId' => $subscriptionCustomerId));
    }

    /**
     * @param \Kairos\SubscriptionBundle\Model\Customer $customer
     */
    public function save(Customer $customer)
    {
        $this->em->persist($customer);
        $this->em->flush();
    }
}