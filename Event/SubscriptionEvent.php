<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kairos\SubscriptionBundle\Event;

use Kairos\SubscriptionBundle\Model\Subscription;
use Symfony\Component\EventDispatcher\Event;

class SubscriptionEvent extends Event
{
    private $webhookContent;
    private $subscription;

    public function __construct(Subscription $subscription, $webhookContent = null)
    {
        $this->subscription = $subscription;
        $this->webhookContent = $webhookContent;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }


    /**
     * @param Subscription $subscription
     * @return $this
     */
    public function setSubscription(Subscription $subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return $webhookContent
     */
    public function getWebhookContent()
    {
        return $this->webhookContent;
    }
}