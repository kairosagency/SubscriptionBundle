<?php

namespace Kairos\SubscriptionBundle\Model;

/**
 * Plan interface
 *
 */
interface PlanInterface extends SyncedInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Add a subscription to a plan
     *
     * @param \Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription
     * @return \Kairos\SubscriptionBundle\Model\PlanInterface
     */
    public function addSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription);

    /**
     * Remove creditCard
     *
     * @param \Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription
     */
    public function removeSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription);

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubscriptions();

    /**
     * Set Subscription plan id
     *
     * @param string $subscriptionPlanId
     *
     */
    public function setSubscriptionPlanId($subscriptionPlanId);

    /**
     * Get Subscription plan id
     *
     * @return string
     */
    public function getSubscriptionPlanId();

    /**
     * set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount();

    /**
     * set trial period
     *
     * @param integer $trialPeriod
     * @return $this
     */
    public function setTrialPeriod($trialPeriod);

    /**
     * Get trial period
     *
     * @return integer
     */
    public function getTrialPeriod();

    /**
     * set trial period unit
     *
     * @param string $trialPeriodUnit
     * @return $this
     */
    public function setTrialPeriodUnit($trialPeriodUnit);

    /**
     * Get trial period unit
     *
     * @return string
     */
    public function getTrialPeriodUnit();

    /**
     * Get trial period interval
     *
     * @return string
     */
    public function getTrialPeriodInterval();
}