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

    /**
     * Set number
     *
     * @param $token
     * @return $this
     */
    public function setNumber($number);

    /**
     * Get token
     *
     * @return string
     */
    public function getNumber();

    /**
     * Set cvv
     *
     * @param $cvv
     * @return $this
     */
    public function setCvv($cvv);

    /**
     * Get cvv
     *
     * @return string
     */
    public function getCvv();

    /**
     * Set expirationDate
     *
     * @param $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate);

    /**
     * Get expirationDate
     *
     * @return string
     */
    public function getExpirationDate();

    /**
     * Set cardholderName
     *
     * @param $cardholderName
     * @return $this
     */
    public function setCardholderName($cardholderName);

    /**
     * Get cardholderName
     *
     * @return string
     */
    public function getCardholderName();

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