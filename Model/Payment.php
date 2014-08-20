<?php

Namespace Kairos\SubscriptionBundle\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class Payment extends Synced implements PaymentInterface
{
    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var \Kairos\SubscriptionBundle\Model\CustomerInterface
     */
    protected $customer;

    /**
     * @var string
     *
     */
    protected $token;

    /**
     * @var string
     *
     */
    protected $nonce;

    /**
     * @var boolean
     *
     */
    protected $defaultCreditCard;

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
     * set customer
     *
     * @param $customer
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
     * Set token
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set nonce
     *
     * @param $nonce
     * @return $this
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * Get nonce
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Set default
     *
     * @param $default
     * @return $this
     */
    public function setDefault($defaultCreditCard)
    {
        $this->defaultCreditCard = $defaultCreditCard;

        return $this;
    }

    /**
     * Get default
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->defaultCreditCard;
    }
} 