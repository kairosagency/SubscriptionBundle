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

    }

    public function onChargeUnsuccesfull(SubscriptionEvent $event)
    {

    }

    public function onPastDue(SubscriptionEvent $event)
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

    public function onTrialEnd(SubscriptionEvent $event)
    {

    }
} 