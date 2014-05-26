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
    private $subscriptionAdapter;

    private $logger;


    public function __construct(SubscriptionAdapterInterface $subscriptionAdapter, Logger $logger)
    {
        $this->logger = $logger;
        $this->subscriptionAdapter = $subscriptionAdapter;
    }

    protected function getSubscriptionAdapter()
    {
        return $this->subscriptionAdapter;
    }


    protected function getLogger()
    {
        return $this->logger;
    }


    public function arrayHasKeys($array = array(), $keys =  array())
    {
        foreach($keys AS $key) {
            if(array_key_exists($key, $array))
                return true;
        }
        return false;
    }

    abstract public function getSubscribedEvents();
}