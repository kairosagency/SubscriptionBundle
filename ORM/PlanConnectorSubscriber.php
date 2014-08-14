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

                $keys = array('amount', 'trialPeriod', 'trialPeriodUnit');
                if($this->arrayHasKeys($changeset, $keys)) {
                    $entity->setSubscriptionSynced(false);
                }

                $this->getLogger()->info('[PlanConnectorSubscriber][onFlush] Scheduled for updates');

                if($entity->getSubscriptionPlanId() && $entity->isSubscriptionSynced() == false) {
                    $this->getSubscriptionAdapter()->updatePlan($entity);
                }
                // in case the object was not already created remotely
                elseif(is_null($entity->getSubscriptionPlanId())) {
                    $this->getSubscriptionAdapter()->createPlan($entity);
                }

                $this->persistAndRecomputeChangeset($em, $uow, $entity);
            }
        }
    }

    /**
     * Returns list of events, that this listener is listening to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }
}