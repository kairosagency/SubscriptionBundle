<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 22/05/2014
 * Time: 16:19
 */

namespace Kairos\SubscriptionBundle\Listener;


use Kairos\SubscriptionBundle\Event\SubscriptionEvent;
use Doctrine\Orm\EntityManager;

class SubscriptionListener {

    protected $transactionClass;

    protected $em;

    public function __construct(EntityManager $em, $transactionClass)
    {
        $this->transactionClass = $transactionClass;

        $this->em = $em;
    }

    public function onChargeSuccesfull(SubscriptionEvent $event)
    {
        $subscription = $event->getSubscription();
        $webhookContent = $event->getWebhookContent();

        if($webhookContent) {
            $transactionRefl = new \ReflectionClass($this->transactionClass);
            $transaction = $transactionRefl->newInstance();

            // should create transaction from webhook content
            $transaction
                ->setSubscriptionTransactionId($webhookContent->subscription->transactions[0]->id)
                ->setSubscriptionTransactionStatus($webhookContent->subscription->transactions[0]->status);

            $subscription->addTransaction($transaction);
            $this->em->persist($transaction);
            $this->em->flush();
            $event->setSubscription($subscription);
        }
    }

    public function onChargeUnsuccesfull(SubscriptionEvent $event)
    {

    }

    public function onCancel(SubscriptionEvent $event)
    {

    }

    public function onExpire(SubscriptionEvent $event)
    {

    }

    public function onActive(SubscriptionEvent $event)
    {

    }

    public function onTrialEnded(SubscriptionEvent $event)
    {

    }
} 