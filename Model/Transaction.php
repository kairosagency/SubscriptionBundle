<?php

Namespace Kairos\SubscriptionBundle\Model;


abstract class Transaction implements TransactionInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    protected $subscription;

    /**
     * @var string
     */
    protected $subscriptionTransactionId;

    /**
     * @var string
     */
    protected $subscriptionTransactionStatus;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set subscription
     *
     * @param $subscription
     * @return $this
     */
    public function setSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get subscription
     *
     * @return \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set Subscription transaction id
     *
     * @param string $subscriptionTransactionId
     *
     */
    public function setSubscriptionTransactionId($subscriptionTransactionId)
    {
        $this->subscriptionTransactionId = $subscriptionTransactionId;

        return $this;
    }

    /**
     * Get Subscription transaction id
     *
     * @return string
     */
    public function getSubscriptionTransactionId()
    {
        return $this->subscriptionTransactionId;
    }

    /**
     * Set Subscription transaction status
     *
     * @param string $subscriptionTransactionStatus
     *
     */
    public function setSubscriptionTransactionStatus($subscriptionTransactionStatus)
    {
        $this->subscriptionTransactionStatus = $subscriptionTransactionStatus;

        return $this;
    }

    /**
     * Get subscriptionTransactionStatus
     *
     * @return string
     */
    public function getSubscriptionTransactionStatus()
    {
        return $this->subscriptionTransactionStatus;
    }
} 