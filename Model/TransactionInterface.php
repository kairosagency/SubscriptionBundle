<?php

Namespace Kairos\SubscriptionBundle\Model;


interface TransactionInterface extends SyncedInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set subscription
     *
     * @param $subscriptionCustomerId
     * @return $this
     */
    public function setSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription);

    /**
     * Get Subscription
     *
     * @return \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    public function getSubscription();

    /**
     * Set Subscription Transaction id
     *
     * @param string $subscriptionId
     *
     */
    public function setSubscriptionTransactionId($subscriptionTransactionId);

    /**
     * Get Subscription Transaction id
     *
     * @return string
     */
    public function getSubscriptionTransactionId();

    /**
     * Set Subscription transaction status
     *
     * @param string $subscriptionTransactionStatus
     *
     */
    public function setSubscriptionTransactionStatus($subscriptionTransactionStatus);

    /**
     * Get subscriptionTransactionStatus
     *
     * @return string
     */
    public function getSubscriptionTransactionStatus();
} 