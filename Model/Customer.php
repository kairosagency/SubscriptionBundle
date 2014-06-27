<?php

Namespace Kairos\SubscriptionBundle\Model;

abstract class Customer extends Synced implements CustomerInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $creditCards;

    /**
     * @var \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    protected $subscription;

    /**
     * @var string
     */
    protected $subscriptionCustomerId;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $companyName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string $website
     *
     */
    protected $website;

    /**
     * @var string $contactPhone
     *
     */
    protected $contactPhone;

    /**
     * @var string $contactMobile
     *
     */
    protected $contactMobile;

    /***                            ***/
    /***       Billing address      ***/
    /***                            ***/

    /**
     * @var string $billingStreet
     *
     */
    protected $billingStreet;

    /**
     * @var string $billingCity
     *
     */
    protected $billingCity;

    /**
     * @var string $billingState
     *
     */
    protected $billingState;

    /**
     * @var string $billingZipcode
     *
     */
    protected $billingZipcode;

    /**
     * @var string $billingCountry
     *
     */
    protected $billingCountry;

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
     * Add credit card
     *
     * @param \Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard
     * @return Plan
     */
    public function addCreditCard(\Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard)
    {
        $creditCard->setCustomer($this);
        $this->creditCards[] = $creditCard;

        return $this;
    }

    /**
     * Remove creditCard
     *
     * @param \Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard
     */
    public function removeCreditCard(\Kairos\SubscriptionBundle\Model\CreditCardInterface $creditCard)
    {
        $this->creditCards->removeElement($creditCard);
    }

    /**
     * Get creditCards
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreditCards()
    {
        return $this->creditCards;
    }

    /**
     * Set subscription
     *
     * @param $subscription
     * @return $this
     */
    public function setSubscription(\Kairos\SubscriptionBundle\Model\SubscriptionInterface $subscription)
    {
        $this->subscription = $subscription;
        $subscription->setCustomer($this);

        return $this;
    }

    /**
     * Get subscription
     *
     * @return \Kairos\SubscriptionBundle\Model\SubscriptionInterface
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Get subscription customer id
     *
     * @param $subscriptionCustomerId
     * @return $this
     */
    public function setSubscriptionCustomerId($subscriptionCustomerId)
    {
        $this->subscriptionCustomerId = $subscriptionCustomerId;

        return $this;
    }

    /**
     * Get subscription customer id
     *
     * @return string
     */
    public function getSubscriptionCustomerId()
    {
        return $this->subscriptionCustomerId;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set company Name
     *
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * Get $companyName
     *
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set website
     *
     * @param string $companyName
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get $website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set contactPhone
     *
     * @param string $contactPhone
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * Get contactPhone
     *
     * @return string
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * Set contactMobile
     *
     * @param string $contactMobile
     */
    public function setContactMobile($contactMobile)
    {
        $this->contactMobile = $contactMobile;

        return $this;
    }

    /**
     * Get contactMobile
     *
     * @return string
     */
    public function getContactMobile()
    {
        return $this->contactMobile;
    }


    /***                            ***/
    /***       Billing address      ***/
    /***                            ***/

    /**
     * Set street
     *
     * @param string $billingStreet
     */
    public function setBillingStreet($billingStreet)
    {
        $this->billingStreet = $billingStreet;

        return $this;
    }


    /**
     * Get street
     *
     * @return string
     */
    public function getBillingStreet()
    {
        return $this->billingStreet;
    }

    /**
     * Set city
     *
     * @param string $billingCity
     */
    public function setBillingCity($billingCity)
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getBillingCity()
    {
        return $this->billingCity;
    }

    /**
     * Set state
     *
     * @param string $billingState
     */
    public function setBillingState($billingState)
    {
        $this->billingState = $billingState;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getBillingState()
    {
        return $this->billingState;
    }

    /**
     * Set zipcode
     *
     * @param string $billingZipcode
     */
    public function setBillingZipcode($billingZipcode)
    {
        $this->billingZipcode = $billingZipcode;

        return $this;
    }

    /**
     * Get zipcode
     *
     * @return string
     */
    public function getBillingZipcode()
    {
        return $this->billingZipcode;
    }

    /**
     * Set country
     *
     * @param string $billingCountry
     */
    public function setbillingCountry($billingCountry)
    {
        $this->billingCountry = $billingCountry;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }
} 