<?php

Namespace Kairos\SubscriptionBundle\Model;

use Doctrine\ORM\Mapping as ORM;

abstract class CreditCard extends Payment implements CreditCardInterface
{
    /**
     * @var string
     *
     */
    protected $maskedNumber;

    /**
     * @var string
     *
     */
    protected $number;

    /**
     * @var string
     *
     */
    protected $cvv;

    /**
     * @var string
     *
     */
    protected $expirationDate;

    /**
     * @var string
     *
     */
    protected $cardholderName;

    /**
     * Set masled number
     *
     * @param $maskedNumber
     * @return $this
     */
    public function setMaskedNumber($maskedNumber)
    {
        $this->maskedNumber = $maskedNumber;

        return $this;
    }

    /**
     * Get masked number
     *
     * @return string
     */
    public function getMaskedNumber()
    {
        return $this->maskedNumber;
    }

    /**
     * Set number
     *
     * @param $token
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set cvv
     *
     * @param $cvv
     * @return $this
     */
    public function setCvv($cvv)
    {
        $this->cvv = $cvv;

        return $this;
    }

    /**
     * Get cvv
     *
     * @return string
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * Set expirationDate
     *
     * @param $expirationDate
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set cardholderName
     *
     * @param $cardholderName
     * @return $this
     */
    public function setCardholderName($cardholderName)
    {
        $this->cardholderName = $cardholderName;

        return $this;
    }

    /**
     * Get cardholderName
     *
     * @return string
     */
    public function getCardholderName()
    {
        return $this->cardholderName;
    }
} 