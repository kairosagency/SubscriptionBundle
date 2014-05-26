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
        $em  = $args->getEntityManager();
        $classMetadata = $em->getClassMetadata(get_class($entity));

        if($entity instanceof Customer) {
            $uow = $em->getUnitOfWork();
            $changeset = $uow->getEntityChangeSet($entity);

            // we unsync only if some properties have changed
            $keys = array('email', 'firstName', 'lastName', 'companyName', 'website');
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
        $em  = $args->getEntityManager();

        if($entity instanceof Customer) {
            $this->getSubscriptionAdapter()->createCustomer($entity);
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
        $em  = $args->getEntityManager();

        // can update only if entity is suported and contact id is set
        if($entity instanceof Customer && $entity->getSubscriptionCustomerId() && $entity->isSubscriptionSynced() == false) {
            $this->getSubscriptionAdapter()->updateCustomer($entity);
            $em->persist($entity);
            $em->flush();
        }
        // in case the object was not created on zoho side
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