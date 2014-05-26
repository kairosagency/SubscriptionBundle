<?php

Namespace Kairos\SubscriptionBundle\Model;


interface CreditCardInterface
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
} 