<?php

/**
 * This file is part of the KnpDoctrineBehaviors package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kairos\SubscriptionBundle\ORM;

use Doctrine\Common\EventSubscriber;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Kairos\SubscriptionBundle\Adapter\SubscriptionAdapterInterface;

abstract class AbstractDoctrineListener implements EventSubscriber
{
    /**
     * @var \Kairos\SubscriptionBundle\Adapter\SubscriptionAdapterInterface
     */
    private $subscriptionAdapter;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;


    public function __construct(SubscriptionAdapterInterface $subscriptionAdapter, Logger $logger)
    {
        $this->logger = $logger;
        $this->subscriptionAdapter = $subscriptionAdapter;
    }

    /**
     * @return SubscriptionAdapterInterface
     */
    protected function getSubscriptionAdapter()
    {
        return $this->subscriptionAdapter;
    }

    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param array $array
     * @param array $keys
     * @return bool
     */
    public function arrayHasKeys($array = array(), $keys =  array())
    {
        foreach($keys AS $key) {
            if(array_key_exists($key, $array))
                return true;
        }
        return false;
    }

    /**
     * @param $em
     * @param $uow
     * @param $entity
     * @param bool $newEntity
     */
    public function persistAndRecomputeChangeset($em, $uow, $entity, $newEntity = false) {
        $classMetadata = $em->getClassMetadata(get_class($entity));
        $em->persist($entity);
        if($newEntity) {
            $uow->computeChangeSet($classMetadata, $entity);
        }
        else {
            $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
        }
    }

    abstract public function getSubscribedEvents();
}