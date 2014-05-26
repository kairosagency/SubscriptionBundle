<?php

Namespace Kairos\SubscriptionBundle\Adapter;

use Kairos\SubscriptionBundle\Model\Customer;
use Kairos\SubscriptionBundle\Model\CreditCard;
use Kairos\SubscriptionBundle\Model\Subscription;
use Kairos\SubscriptionBundle\Model\Plan;

interface SubscriptionAdapterInterface
{
    public function getLogger();

    public function getAdapterName();

    /****** plan adapter ********/

    public function createPlan(Plan $plan);

    public function getPlan(Plan $plan);

    public function updatePlan(Plan $plan);

    public function deletePlan(Plan $plan);


    /****** customer adapter ********/

    public function createCustomer(Customer $customer);

    public function getCustomer(Customer $customer);

    public function updateCustomer(Customer $customer);

    public function deleteCustomer(Customer $customer);


    /****** credit card ********/

    public function createCreditCard(CreditCard $creditCard);

    public function getCreditCard(CreditCard $creditCard);

    public function updateCreditCard(CreditCard $creditCard);

    public function deleteCreditCard(CreditCard $creditCard);


    /****** Subscriptions ********/

    public function createSubscription(Subscription $subscription, $options = array());

    public function getSubscription(Subscription $subscription, $options = array());

    public function updateSubscription(Subscription $subscription, $options = array());

    public function cancelSubscription(Subscription $subscription, $options = array());

    public function retryCharge(Subscription $subscription, $options = array());

} 