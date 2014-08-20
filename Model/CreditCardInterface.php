<?php

Namespace Kairos\SubscriptionBundle\Model;


interface CreditCardInterface
{
    /**
     * Set masled number
     *
     * @param $maskedNumber
     * @return $this
     */
    public function setMaskedNumber($maskedNumber);

    /**
     * Get masked number
     *
     * @return string
     */
    public function getMaskedNumber();

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
} 