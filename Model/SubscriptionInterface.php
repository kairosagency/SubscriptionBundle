<?php

Namespace Kairos\SubscriptionBundle\Model;


interface SubscriptionInterface extends SyncedInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * set adapterName
     *
     * @param $adapterName
     * @return $this
     */
    public function setAdapterName($adapterName);

    /**
     * Get id
     *
     * @return string
     */
    public function getAdapterName();

    /**
     * Get subscription customer id
     *
     * @param $subscriptionCustomerId
     * @return $this
     */
    public function setCustomer(\Kairos\SubscriptionBundle\Model\CustomerInterface $customer);

    /**
     * Get customer
     *
     * @return \Kairos\SubscriptionBundle\Model\CustomerInterface
     */
    public function getCustomer();

    /**
     * Set the plan
     *
     * @param $plan
     * @return $this
     */
    public function setPlan(\Kairos\SubscriptionBundle\Model\PlanInterface $plan);

    /**
     * Get the plan
     *
     * @return \Kairos\SubscriptionBundle\Model\PlanInterface
     */
    public function getPlan();

    /**
     * Add credit card
     *
     * @param \Kairos\SubscriptionBundle\Model\TransactionInterface $transactions
     * @return \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    public function addTransaction(\Kairos\SubscriptionBundle\Model\TransactionInterface $transaction);

    /**
     * Remove creditCard
     *
     * @param \Kairos\SubscriptionBundle\Model\TransactionInterface $creditCard
     */
    public function removeTransaction(\Kairos\SubscriptionBundle\Model\TransactionInterface $transaction);

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions();

    /**
     * Set Subscription id
     *
     * @param string $subscriptionId
     *
     */
    public function setSubscriptionId($subscriptionId);

    /**
     * Get Subscription id
     *
     * @return string
     */
    public function getSubscriptionId();

    /**
     * Set subscription status
     *
     * @param string $status
     *
     */
    public function setStatus($status);

    /**
     * Get subscription status
     *
     * @return string $status
     *
     */
    public function getStatus();

    /**
     * Is subscription canceled ?
     *
     * @return boolean
     */
    public function isCanceled();

    /**
     * Is subscription active ?
     *
     * @return boolean
     */
    public function isActive();

} 