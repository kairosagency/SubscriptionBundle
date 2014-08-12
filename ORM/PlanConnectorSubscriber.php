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
    Doctrine\ORM\Event\OnFlushEventArgs,
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
        $entity = $args->getEntity();

        if ($entity instanceof Plan) {
            $this->getLogger()->info('[PlanConnectorSubscriber] preUpdate');

            $em  = $args->getEntityManager();
            $classMetadata = $em->getClassMetadata(get_class($entity));
            $uow = $em->getUnitOfWork();
            $changeset = $uow->getEntityChangeSet($entity);

            // we unsync only if these properties have changed
            // this will trigger a postUpdate event
            $keys = array('amount', 'trialPeriod', 'trialPeriodUnit');
            if($this->arrayHasKeys($changeset, $keys)) {
                $entity->setSubscriptionSynced(false);
                $em->persist($entity);
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof Plan) {
                $this->getLogger()->info('[PlanConnectorSubscriber][onFlush] Scheduled for insertion');

                $this->getSubscriptionAdapter()->createPlan($entity);
                $this->persistAndRecomputeChangeset($em, $uow, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if($entity instanceof Plan) {
                $this->getLogger()->info('[PlanConnectorSubscriber][onFlush] Scheduled for updates');

                if($entity->getSubscriptionPlanId() && $entity->isSubscriptionSynced() == false) {
                    $this->getSubscriptionAdapter()->updatePlan($entity);
                    $this->persistAndRecomputeChangeset($em, $uow, $entity);
                }
                // in case the object was not already created remotely
                elseif(is_null($entity->getSubscriptionPlanId())) {
                    $this->getSubscriptionAdapter()->createPlan($entity);
                    $this->persistAndRecomputeChangeset($em, $uow, $entity);
                }
            }
        }
    }


    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if($entity instanceof Plan) {
            $this->getLogger()->info('[PlanConnectorSubscriber] postPersist');

            $this->getSubscriptionAdapter()->createPlan($entity);
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
        if($entity instanceof Plan && $entity->getSubscriptionPlanId() && $entity->isSubscriptionSynced() == false) {
            $this->getLogger()->info('[PlanConnectorSubscriber] postUpdate');

            $this->getSubscriptionAdapter()->updatePlan($entity);
            $em  = $args->getEntityManager();
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
        return [Events::preUpdate, Events::onFlush];
        //return [Events::preUpdate, Events::postUpdate, Events::postPersist];
    }
}