<?php

Namespace Kairos\SubscriptionBundle\Model;


interface PaymentInterface extends SyncedInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

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
     * Set token
     *
     * @param $token
     * @return $this
     */
    public function setToken($token);

    /**
     * Get token
     *
     * @return string
     */
    public function getToken();

    /**
     * Set nonce
     *
     * @param $nonce
     * @return $this
     */
    public function setNonce($nonce);

    /**
     * Get nonce
     *
     * @return string
     */
    public function getNonce();

    /**
     * Set default
     *
     * @param $default
     * @return $this
     */
    public function setDefault($default);

    /**
     * Get default
     *
     * @return boolean
     */
    public function isDefault();
} 