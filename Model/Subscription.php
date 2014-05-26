<?php

Namespace Kairos\SubscriptionBundle\Model;

abstract class Subscription extends Synced implements SubscriptionInterface
{
    CONST ACTIVE    = 'Active';
    CONST CANCELED  = 'Canceled';
    CONST EXPIRED   = 'Expired';
    CONST PAST_DUE  = 'Past Due';
    CONST PENDING   = 'Pending';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $adapterName;

    /**
     * @var \Kairos\SubscriptionBundle\Model\CustomerInterface
     */
    protected $customer;

    /**
     * @var \Kairos\SubscriptionBundle\Model\PlanInterface
     */
    protected $plan;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $transactions;

    /**
     * @var string
     */
    protected $subscriptionId;

    /**
     * @var string
     */
    protected $status;

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
     * Get subscription customer id
     *
     * @param $subscriptionCustomerId
     * @return $this
     */
    public function setCustomer(\Kairos\SubscriptionBundle\Model\CustomerInterface $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get customer
     *
     * @return \Kairos\SubscriptionBundle\Model\CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set the plan
     *
     * @param $plan
     * @return $this
     */
    public function setPlan(\Kairos\SubscriptionBundle\Model\PlanInterface $plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get the plan
     *
     * @return \Kairos\SubscriptionBundle\Model\PlanInterface
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Add credit card
     *
     * @param \Kairos\SubscriptionBundle\Model\TransactionInterface $transactions
     * @return \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    public function addTransaction(\Kairos\SubscriptionBundle\Model\TransactionInterface $transaction)
    {
        $transaction->setSubscription($this);
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove creditCard
     *
     * @param \Kairos\SubscriptionBundle\Model\TransactionInterface $creditCard
     */
    public function removeTransaction(\Kairos\SubscriptionBundle\Model\TransactionInterface $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }


    /**
     * set adapterName
     *
     * @param $adapterName
     * @return $this
     */
    public function setAdapterName($adapterName)
    {
        $this->adapterName = $adapterName;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getAdapterName()
    {
        return $this->adapterName;
    }

    /**
     * Set Subscription id
     *
     * @param string $subscriptionId
     *
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    /**
     * Get Subscription id
     *
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get Subscription id
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Is canceled ?
     *
     * @return boolean
     */
    public function isCanceled()
    {
        return $this->getStatus() === self::CANCELED ? true : false ;
    }

    /**
     * Is active ?
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->getStatus() === self::ACTIVE ? true : false ;
    }
} 