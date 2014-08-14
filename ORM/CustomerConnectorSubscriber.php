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
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof Customer) {
                $this->getLogger()->info('[CustomerConnectorSubscriber][onFlush] Scheduled for insertion');

                $this->getSubscriptionAdapter()->createCustomer($entity);
                $this->persistAndRecomputeChangeset($em, $uow, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if($entity instanceof Customer) {
                $this->getLogger()->info('[CustomerConnectorSubscriber][onFlush] Scheduled for updates');

                $changeset = $uow->getEntityChangeSet($entity);
                $keys = array('email', 'firstName', 'lastName', 'companyName', 'billingCity', 'billingCountry', 'billingStreet');
                if($this->arrayHasKeys($changeset, $keys)) {
                    $entity->setSubscriptionSynced(false);
                }

                if($entity->getSubscriptionCustomerId() && $entity->isSubscriptionSynced() == false) {
                    $this->getSubscriptionAdapter()->updateCustomer($entity);
                }
                // in case the object was not already created remotely
                elseif(is_null($entity->getSubscriptionCustomerId())) {
                    $this->getSubscriptionAdapter()->createCustomer($entity);
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