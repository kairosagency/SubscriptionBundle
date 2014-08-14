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

    public function createPlan(Plan $plan, $options = array());

    public function getPlan(Plan $plan, $options = array());

    public function updatePlan(Plan $plan, $options = array());

    public function deletePlan(Plan $plan, $options = array());


    /****** customer adapter ********/

    public function createCustomer(Customer $customer, $options = array());

    public function getCustomer(Customer $customer, $options = array());

    public function updateCustomer(Customer $customer, $options = array());

    public function deleteCustomer(Customer $customer, $options = array());


    /****** credit card ********/

    public function createCreditCard(CreditCard $creditCard, $options = array());

    public function getCreditCard(CreditCard $creditCard, $options = array());

    public function updateCreditCard(CreditCard $creditCard, $options = array());

    public function deleteCreditCard(CreditCard $creditCard, $options = array());


    /****** Subscriptions ********/

    public function createSubscription(Subscription $subscription, $options = array());

    public function getSubscription(Subscription $subscription, $options = array());

    public function updateSubscription(Subscription $subscription, $options = array());

    public function cancelSubscription(Subscription $subscription, $options = array());

    public function retryCharge(Subscription $subscription, $options = array());

} 