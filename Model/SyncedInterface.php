<?php

namespace Kairos\SubscriptionBundle\Model;


interface SyncedInterface
{
    /**
     * Set synced
     *
     * @param boolean $synced
     *
     */
    public function setSubscriptionSynced($subscriptionSynced);

    /**
     * is synced ?
     *
     * @return boolean
     */
    public function isSubscriptionSynced();

    /**
     * Set synced
     *
     * @param \DateTime $syncedTimestamp
     *
     */
    public function setSubscriptionSyncedTimestamp($subscriptionSyncedTimestamp);

    /**
     *
     * @return \DateTime
     */
    public function getSubscriptionSyncedTimestamp();

    /**
     * Refresh timestamp
     */
    public function refreshSubscriptionSyncedTimestamp();

    /**
     *
     * @return array
     */
    public function getErrors();

    /**
     *
     * @return boolean
     */
    public function hasErrors();

    /**
     * Set $errors
     */
    public function setErrors($errors);
}