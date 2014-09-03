<?php

Namespace Kairos\SubscriptionBundle\Adapter;

use Kairos\SubscriptionBundle\Model\CustomerInterface;
use Kairos\SubscriptionBundle\Model\PaymentInterface;
use Kairos\SubscriptionBundle\Model\SubscriptionInterface;
use Kairos\SubscriptionBundle\Model\PlanInterface;

interface SubscriptionAdapterInterface
{
    public function getLogger();

    public function getAdapterName();

    /****** plan adapter ********/

    public function createPlan(PlanInterface $plan, $options = array());

    public function getPlan(PlanInterface $plan, $options = array());

    public function updatePlan(PlanInterface $plan, $options = array());

    public function deletePlan(PlanInterface $plan, $options = array());


    /****** customer adapter ********/

    public function createCustomer(CustomerInterface $customer, $options = array());

    public function getCustomer(CustomerInterface $customer, $options = array());

    public function updateCustomer(CustomerInterface $customer, $options = array());

    public function deleteCustomer(CustomerInterface $customer, $options = array());


    /****** payments ********/

    public function createPayment(PaymentInterface $payment, $options = array());

    public function getPayment(PaymentInterface $payment, $options = array());

    public function updatePayment(PaymentInterface $payment, $options = array());

    public function deletePayment(PaymentInterface $payment, $options = array());


    /****** subscriptions ********/

    public function createSubscription(SubscriptionInterface $subscription, $options = array());

    public function getSubscription(SubscriptionInterface $subscription, $options = array());

    public function updateSubscription(SubscriptionInterface $subscription, $options = array());

    public function deleteSubscription(SubscriptionInterface $subscription, $options = array());

    public function retryCharge(SubscriptionInterface $subscription, $options = array());

} 