<?php

/*
 * This file is part of the KnpDoctrineBehaviors package.
 *
 * (c) KnpLabs <http://knplabs.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kairos\SubscriptionBundle\ORM;

use Symfony\Bridge\Monolog\Logger;

use Doctrine\Common\Persistence\Mapping\ClassMetadata,
    Doctrine\ORM\Event\PreUpdateEventArgs,
    Doctrine\ORM\Event\LifecycleEventArgs,
    Doctrine\ORM\Events;

use Kairos\SubscriptionBundle\Model\Customer;

/**
 * ContactConnector Doctrine2 listener.
 *
 * Listens to onFlush event and marks SoftDeletable entities
 * as deleted instead of really removing them.
 */
class CustomerConnectorSubscriber extends AbstractDoctrineListener
{

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Customer) {
            $this->getLogger()->info('[CustomerConnectorSubscriber] preUpdate');
            $em  = $args->getEntityManager();
            $classMetadata = $em->getClassMetadata(get_class($entity));
            $uow = $em->getUnitOfWork();
            $changeset = $uow->getEntityChangeSet($entity);

            // we unsync only if these properties have changed
            // this will trigger a postUpdate event
            $keys = array('email', 'firstName', 'lastName', 'companyName', 'billingCity', 'billingCountry', 'billingStreet');
            if($this->arrayHasKeys($changeset, $keys)) {
                $entity->setSubscriptionSynced(false);
                $em->persist($entity);
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }


    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Customer) {
            $this->getLogger()->info('[CustomerConnectorSubscriber] postPersist');

            $this->getSubscriptionAdapter()->createCustomer($entity);
            $em  = $args->getEntityManager();
            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // can update only if entity is suported and contact id is set
        if($entity instanceof Customer && $entity->getSubscriptionCustomerId() && $entity->isSubscriptionSynced() == false) {
            $this->getLogger()->info('[CustomerConnectorSubscriber] postUpdate');

            $this->getSubscriptionAdapter()->updateCustomer($entity);
            $em  = $args->getEntityManager();
            $em->persist($entity);
            $em->flush();
        }
        // in case the object was not already created remotely
        elseif($entity instanceof Customer && is_null($entity->getSubscriptionCustomerId())) {
            $this->postPersist($args);
        }
    }

    /**
     * Returns list of events, that this listener is listening to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::preUpdate, Events::postUpdate, Events::postPersist];
    }
}