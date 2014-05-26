<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 15/05/2014
 * Time: 14:25
 */

namespace Kairos\SubscriptionBundle\Model;


interface CustomerInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Add credit card
     *
     * @param \Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard
     * @return \Kairos\SubscriptionBundle\Model\CustomerInterface
     */
    public function addCreditCard(\Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard);

    /**
     * Remove creditCard
     *
     * @param \Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard
     */
    public function removeCreditCard(\Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard);

    /**
     * Get creditCards
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditCards();

    /**
     * Set subscription
     *
     * @param $subscription
     * @return $this
     */
    public function setSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription);

    /**
     * Get subscription
     *
     * @return \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    public function getSubscription();

    /**
     * Get subscription customer id
     *
     * @param $subscriptionCustomerId
     * @return $this
     */
    public function setSubscriptionCustomerId($subscriptionCustomerId);

    /**
     * Get subscription customer id
     *
     * @return string
     */
    public function getSubscriptionCustomerId();

    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set company name
     *
     * @param string $companyName
     */
    public function setCompanyName($companyName);

    /**
     * Get $companyName
     *
     * @return string
     */
    public function getCompanyName();

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email);

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set website
     *
     * @param string $companyName
     */
    public function setWebsite($website);

    /**
     * Get $website
     *
     * @return string
     */
    public function getWebsite();

    /**
     * Set contactPhone
     *
     * @param string $contactPhone
     */
    public function setContactPhone($contactPhone);

    /**
     * Get contactPhone
     *
     * @return string
     */
    public function getContactPhone();

    /**
     * Set contactMobile
     *
     * @param string $contactMobile
     */
    public function setContactMobile($contactMobile);

    /**
     * Get contactMobile
     *
     * @return string
     */
    public function getContactMobile();


    /***                            ***/
    /***       Billing address      ***/
    /***                            ***/

    /**
     * Set street
     *
     * @param string $billingStreet
     */
    public function setBillingStreet($billingStreet);


    /**
     * Get street
     *
     * @return string
     */
    public function getBillingStreet();

    /**
     * Set city
     *
     * @param string $billingCity
     */
    public function setBillingCity($billingCity);

    /**
     * Get city
     *
     * @return string
     */
    public function getBillingCity();

    /**
     * Set state
     *
     * @param string $billingState
     */
    public function setBillingState($billingState);

    /**
     * Get state
     *
     * @return string
     */
    public function getBillingState();

    /**
     * Set zipcode
     *
     * @param string $billingZipcode
     */
    public function setBillingZipcode($billingZipcode);

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getBillingZipcode();

    /**
     * Set country
     *
     * @param string $billingCountry
     */
    public function setbillingCountry($billingCountry);

    /**
     * Get country
     *
     * @return string
     */
    public function getBillingCountry();
} 