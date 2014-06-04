<?php

namespace Kairos\SubscriptionBundle\Model;

/**
 * Plan abstract class
 *
 */
abstract class Plan extends Synced implements PlanInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $subscriptions;

    /**
     * @var string
     */
    protected $subscriptionPlanId;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var integer
     */
    protected $trialPeriod;

    /**
     * @var string
     */
    protected $trialPeriodUnit;

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
     * Add a subscription to a plan
     *
     * @param \Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription
     * @return \Kairos\SubscriptionBundle\Model\PlanInterface
     */
    public function addSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription)
    {
        $subscription->setPlan($this);
        $this->subscriptions[] = $subscription;

        return $this;
    }

    /**
     * Remove creditCard
     *
     * @param \Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription
     */
    public function removeSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription)
    {
        $this->subscriptions->removeElement($subscription);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }


    /**
     * Set Subscription plan id
     *
     * @param string $subscriptionPlanId
     *
     */
    public function setSubscriptionPlanId($subscriptionPlanId)
    {
        $this->subscriptionPlanId = $subscriptionPlanId;

        return $this;
    }

    /**
     * Get Subscription plan id
     *
     * @return string
     */
    public function getSubscriptionPlanId()
    {
        return $this->subscriptionPlanId;
    }

    /**
     * set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * set trial period
     *
     * @param integer $trialPeriod
     * @return $this
     */
    public function setTrialPeriod($trialPeriod)
    {
        $this->trialPeriod = $trialPeriod;

        return $this;
    }

    /**
     * Get trial period
     *
     * @return integer
     */
    public function getTrialPeriod()
    {
        return $this->trialPeriod;
    }

    /**
     * set trial period unit
     *
     * @param string $trialPeriodUnit
     * @return $this
     */
    public function setTrialPeriodUnit($trialPeriodUnit)
    {
        $this->trialPeriodUnit = $trialPeriodUnit;

        return $this;
    }

    /**
     * Get trial period unit
     *
     * @return string
     */
    public function getTrialPeriodUnit()
    {
        return $this->trialPeriodUnit;
    }

    /**
     * Get trial period interval
     *
     * @return string
     */
    public function getTrialPeriodInterval()
    {
        return new \DateInterval('P'.$this->getTrialPeriod().$this->getTrialPeriodInterval());
    }
}