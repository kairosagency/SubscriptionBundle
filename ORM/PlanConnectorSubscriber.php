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

use Kairos\SubscriptionBundle\Model\Plan;
/**
 * ItemConnector Doctrine2 listener.
 *
 * Listens to onFlush event and marks SoftDeletable entities
 * as deleted instead of really removing them.
 */
class PlanConnectorSubscriber extends AbstractDoctrineListener
{
    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->getLogger()->err('[PlanConnectorSubscriber] preUpdate');
        $entity = $args->getEntity();
        $em  = $args->getEntityManager();
        $classMetadata = $em->getClassMetadata(get_class($entity));

        if ($entity instanceof Plan) {
            $uow = $em->getUnitOfWork();
            $changeset = $uow->getEntityChangeSet($entity);

            $keys = array('amount', 'trialPeriod', 'trialPeriodUnit');
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
        $this->getLogger()->err('[PlanConnectorSubscriber] postPersist');
        $entity = $args->getEntity();
        $em  = $args->getEntityManager();

        if($entity instanceof Plan) {
            $this->getSubscriptionAdapter()->createPlan($entity);
            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->getLogger()->err('[PlanConnectorSubscriber] postUpdate');
        $entity = $args->getEntity();
        $em  = $args->getEntityManager();

        // can update only if entity is suported and contact id is set
        if($entity instanceof Plan && $entity->getSubscriptionPlanId() && $entity->isSubscriptionSynced() == false) {
            $this->getSubscriptionAdapter()->updatePlan($entity);
            $em->persist($entity);
            $em->flush();
        }
        // in case the object was not created on zoho side
        elseif($entity instanceof Plan && is_null($entity->getSubscriptionPlanId())) {
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