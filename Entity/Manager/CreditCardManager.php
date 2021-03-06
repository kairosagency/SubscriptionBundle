<?php
namespace Kairos\SubscriptionBundle\Entity\Manager;

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\EntityRepository;

use Kairos\SubscriptionBundle\Model\CreditCard;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class CreditCardManager
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
     * @param string $token
     * @return array
     */
    public function findByCreditCardByToken($token)
    {
        return $this->repository->findBy(array('token' => $token));
    }

    /**
     * @param \Kairos\SubscriptionBundle\Model\CreditCard $creditCard
     */
    public function save(CreditCard $creditCard)
    {
        $this->em->persist($creditCard);
        $this->em->flush();
    }


    /**
     * @param \Kairos\SubscriptionBundle\Model\CreditCard $creditCard
     */
    public function saveAndResetDefaultCreditCard(CreditCard $creditCard)
    {
        $qb = $this->repository->createQueryBuilder('cc')
            ->update('cc')
            ->set('defaultCreditCard', false)
            ->where('cc.customer = :customer')
            ->setParameters(
                array(
                    'customer' => $creditCard->getCustomer(),
                )
            );
        $result = $qb->getQuery()->getResult();
        $this->em->persist($creditCard);
        $this->em->flush();
    }

    /**
     * @param \Kairos\SubscriptionBundle\Model\CreditCard $creditCard
     */
    public function resetDefaultCreditCard(CreditCard $creditCard)
    {
        $qb = $this->repository->createQueryBuilder('cc');

        $qb->update()
            ->set('cc.defaultCreditCard', $qb->expr()->literal(false))
            ->where('cc.customer = :customer')
            ->andWhere('cc != :creditcard')
            ->setParameters(
                array(
                    'customer' => $creditCard->getCustomer(),
                    'creditcard' => $creditCard,
                )
            );
        $result = $qb->getQuery()->getResult();
    }
}