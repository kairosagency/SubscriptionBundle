<?php

namespace Kairos\SubscriptionBundle\Model;


abstract class Synced implements SyncedInterface
{

    /**
     * @var boolean $subscriptionSynced
     *
     */
    protected $subscriptionSynced;

    /**
     * @var \DateTime $subscriptionSyncedTimestamp
     *
     */
    protected $subscriptionSyncedTimestamp;

    /**
     * @var array $errors
     *
     */
    protected $errors;

    /**
     * Set synced
     *
     * @param boolean $subscriptionSynced
     *
     */
    public function setSubscriptionSynced($subscriptionSynced)
    {
        $this->subscriptionSynced = $subscriptionSynced;

        return $this;
    }

    /**
     * is synced ?
     *
     * @return boolean
     */
    public function isSubscriptionSynced()
    {
        return $this->subscriptionSynced;
    }

    /**
     * Set synced
     *
     * @param \DateTime $syncedTimestamp
     *
     */
    public function setSubscriptionSyncedTimestamp($subscriptionSyncedTimestamp)
    {
        $this->subscriptionSyncedTimestamp = $subscriptionSyncedTimestamp;

        return $this;
    }

    /**
     *
     * @return \DateTime
     */
    public function getSubscriptionSyncedTimestamp()
    {
        return $this->subscriptionSyncedTimestamp;
    }

    /**
     * Refresh timestamp
     */
    public function refreshSubscriptionSyncedTimestamp()
    {
        $this->subscriptionSyncedTimestamp = new \DateTime('now');

        return $this;
    }

    /**
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Set $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }
}