<?php

Namespace Kairos\SubscriptionBundle\Adapter;

use Doctrine\ORM\EntityManager;

use Kairos\SubscriptionBundle\Event\SubscriptionEvent;
use Kairos\SubscriptionBundle\KairosSubscriptionEvents;
use Kairos\SubscriptionBundle\Model\Transaction;
use Kairos\SubscriptionBundle\Model\Customer;
use Kairos\SubscriptionBundle\Model\CreditCard;
use Kairos\SubscriptionBundle\Model\Subscription;
use Kairos\SubscriptionBundle\Model\Plan;

use Braintree_Configuration,
    Braintree_Exception,
    Braintree_Customer,
    Braintree_CreditCard,
    Braintree_Subscription,
    Braintree_WebhookNotification;

use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;


class BraintreeSubscriptionAdapter implements SubscriptionAdapterInterface
{
    CONST ADAPTER_NAME = 'braintree';

    protected $transactionClass;

    private $logger;

    public function __construct(Logger $logger, $environment, $merchantId, $publicKey, $privateKey, $transactionClass)
    {
        Braintree_Configuration::environment($environment);
        Braintree_Configuration::merchantId($merchantId);
        Braintree_Configuration::publicKey($publicKey);
        Braintree_Configuration::privateKey($privateKey);
        $this->transactionClass = $transactionClass;
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function getAdapterName()
    {
        return self::ADAPTER_NAME;
    }


    /****** plan adapter ********/

    /**
     * @param Plan $plan
     * @return Plan
     */
    public function createPlan(Plan $plan, $options = array())
    {
        $plan->setSubscriptionSynced(true);
        return $plan;
    }

    /**
     * @param Plan $plan
     * @return Plan
     */
    public function getPlan(Plan $plan, $options = array())
    {
        return $plan;
    }

    /**
     * @param Plan $plan
     * @return Plan
     */
    public function updatePlan(Plan $plan, $options = array())
    {
        $plan->setSubscriptionSynced(true);
        return $plan;
    }

    public function deletePlan(Plan $plan, $options = array())
    {

    }



    /****** customer adapter ********/

    /**
     * @param Customer $customer
     * @return Customer
     */
    public function createCustomer(Customer $customer, $options = array())
    {
        try {
            $result = Braintree_Customer::create(
                $this->serializeCustomer($customer, $options)
            );

            if ($result->success) {
                $customer
                    ->setSubscriptionCustomerId($result->customer->id)
                    ->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][createCustomer] Sucess', array('customer id' => $customer->getSubscriptionCustomerId()), $this->serializeCustomer($customer, $options));
            }
            else {
                $this->getLogger()->Error('[Braintree][createCustomer] Error', array('customer id' => $customer->getSubscriptionCustomerId()), $result->errors->deepAll());
                $customer->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createCustomer] ' . $e->getMessage());
        }

        return $customer;
    }

    /**
     * @param Customer $customer
     * @return object
     */
    public function getCustomer(Customer $customer, $options = array())
    {
        return Braintree_Customer::find($customer->getSubscriptionCustomerId());
    }

    /**
     * @param Customer $customer
     * @return Customer
     */
    public function updateCustomer(Customer $customer, $options = array())
    {
        try {
            $result = Braintree_Customer::update(
                $customer->getSubscriptionCustomerId(),
                $this->serializeCustomer($customer, $options)
            );

            if ($result->success) {
                $customer->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][updateCustomer] Sucess', array('customer id' => $customer->getSubscriptionCustomerId()), $this->serializeCustomer($customer, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][updateCustomer] Error', array('customer id' => $customer->getSubscriptionCustomerId()), $result->errors->deepAll());
                $customer->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][updateCustomer] ' . $e->getMessage());
        }


        return $customer;
    }

    public function deleteCustomer(Customer $customer, $options = array())
    {
        return Braintree_Customer::delete($customer->getSubscriptionCustomerId());
    }


    /****** credit card ********/

    /**
     * @param CreditCard $creditCard
     * @return CreditCard
     */
    public function createCreditCard(CreditCard $creditCard, $options = array())
    {
        try {
            $result = Braintree_CreditCard::create(
                $this->serializeCreditCard($creditCard, $options)
            );

            //var_dump($result);exit;
            if ($result->success) {
                $creditCard->setToken($result->creditCard->token);
                $creditCard->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][createCreditCard] Sucess', array('credit card token' => $creditCard->getToken()), $this->serializeCreditCard($creditCard, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][createCreditCard] Error', array('credit card token' => $creditCard->getToken()), $result->errors->deepAll());
                $creditCard->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createCreditCard] ' . $e->getMessage());
        }
        return $creditCard;
    }

    /**
     * @param CreditCard $creditCard
     * @return object
     */
    public function getCreditCard(CreditCard $creditCard, $options = array())
    {
        return Braintree_CreditCard::find($creditCard->getToken());
    }

    /**
     * @param CreditCard $creditCard
     * @return CreditCard
     */
    public function updateCreditCard(CreditCard $creditCard, $options = array())
    {
        try {
            $result = Braintree_CreditCard::update(
                $creditCard->getToken(),
                $this->serializeCreditCard($creditCard, $options)
            );

            if ($result->success) {
                $creditCard->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][updateCreditCard] Sucess', array('credit card token' => $creditCard->getToken()), $this->serializeCreditCard($creditCard, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][updateCreditCard] Error', array('credit card token' => $creditCard->getToken()), $result->errors->deepAll());
                $creditCard->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][updateCreditCard] ' . $e->getMessage());
        }

        return $creditCard;
    }

    public function deleteCreditCard(CreditCard $creditCard, $options = array())
    {

    }

    /****** Subscriptions ********/

    /**
     * @param Subscription $subscription
     * @param array $options
     * @return Subscription
     */
    public function createSubscription(Subscription $subscription, $options = array())
    {
        try {
            $result = Braintree_Subscription::create(
                $this->serializeSubscription($subscription, $options)
            );

            if($result->success) {
                $this->getLogger()->info('[Braintree][createSubscription] Success', array('subscription id' => $subscription->getSubscriptionId()), $this->serializeSubscription($subscription, $options));
                $subscription
                    ->setAdapterName($this->getAdapterName())
                    ->setSubscriptionId($result->subscription->id)
                    ->setSubscriptionSynced(true)
                    ->setStatus($result->subscription->status);

                $transactionRefl = new \ReflectionClass($this->transactionClass);

                foreach($result->subscription->transactions AS $transactionResult) {
                    $transaction = $transactionRefl->newInstance();
                    // should create transaction from webhook content
                    $transaction
                        ->setSubscriptionTransactionId($transactionResult->id)
                        ->setSubscriptionTransactionStatus($transactionResult->status);

                    $subscription->addTransaction($transaction);
                }

            }
            else {
                $this->getLogger()->error('[Braintree][createSubscription] Error', $result->errors->deepAll());
                $subscription->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createSubscription] ' . $e->getMessage());
        }

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     * @param array $options
     * @return Braintree_Subscription
     */
    public function getSubscription(Subscription $subscription, $options = array())
    {
        return Braintree_Subscription::find($subscription->getSubscriptionId());
    }

    public function updateSubscription(Subscription $subscription, $options = array())
    {
        try {
            if ($subscription->getAdapterName() !== $this->getAdapterName()) {
                throw new Braintree_Exception('Adapter mismatch');
            }

            $result = Braintree_Subscription::update(
                $subscription->getSubscriptionId(),
                $this->serializeSubscription($subscription, $options)
            );

            if($result->success) {
                $subscription
                    ->setSubscriptionSynced(true)
                    ->setStatus($result->subscription->status);
                $this->getLogger()->info('[Braintree][updateSubscription] Sucess', array('subscription id' => $subscription->getSubscriptionId()), $this->serializeSubscription($subscription, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][updateSubscription] Error', $result->errors->deepAll());
                $subscription->setErrors($result->errors->deepAll());
            }


        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][updateSubscription] ' . $e->getMessage());
        }

        return $subscription;
    }

    //todo : check the result
    public function cancelSubscription(Subscription $subscription, $options = array())
    {
        try {
            if ($subscription->getAdapterName() !== $this->getAdapterName()) {
                throw new Braintree_Exception('Adapter mismatch');
            }

            $result = Braintree_Subscription::cancel($subscription->getSubscriptionId());

            if($result->success) {
                $subscription
                    ->setSubscriptionSynced(true)
                    ->setStatus($result->subscription->status);
                $this->getLogger()->info('[Braintree][cancelSubscription] Success', array('subscription id' => $subscription->getSubscriptionId()), $this->serializeSubscription($subscription, $options));
            }
            else {
                $subscription->setErrors($result->errors->deepAll());
                $this->getLogger()->error('[Braintree][cancelSubscription] Error', $result->errors->deepAll());
            }


        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][cancelSubscription] ' . $e->getMessage());
        }

        return $subscription;
    }

    public function retryCharge(Subscription $subscription, $options = array())
    {

    }

    /**** webhooks ****/

    /**
     * @param Request $request
     * @return Braintree_WebhookNotification
     */
    public function parseWebhook(Request $request)
    {
        return Braintree_WebhookNotification::parse(
            $request->request->get('bt_signature'),
            $request->request->get('bt_payload')
        );
    }

    /**
     * @param Subscription $subscription
     * @param $notification
     * @return null|string
     */
    public function getSubscriptionEvent(Subscription $subscription, $notification)
    {
        $eventName = null;

        switch ($notification->kind) {
            case 'subscription_canceled' :
                $subscription->setStatus(Subscription::CANCELED);
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_CANCELED;
                break;
            case 'subscription_charged_successfully' :
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_CHARGED_SUCCESSFULLY;
                break;
            case 'subscription_charged_unsuccessfully' :
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_CHARGED_UNSUCCESSFULLY;
                break;
            case 'subscription_expired' :
                $subscription->setStatus(Subscription::EXPIRED);
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_EXPIRED;
                break;
            case 'subscription_trial_ended' :
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_TRIAL_ENDED;
                break;
            case 'subscription_went_active' :
                $subscription->setStatus(Subscription::ACTIVE);
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_ACTIVED;
                break;
            case 'subscription_went_past_due' :
                $subscription->setStatus(Subscription::PAST_DUE);
                $eventName = KairosSubscriptionEvents::SUBSCRIPTION_PAST_DUE;
                break;
        }

        return $eventName;
    }

    public function verifyWebhook($challenge)
    {
        return Braintree_WebhookNotification::verify($challenge);
    }

    /**** serialization helper ****/

    public function serializeCustomer(Customer $customer, $options = array())
    {
        $result = array();

        if ($customer->getFirstName()) {
            $result['firstName'] = $customer->getFirstName();
        }

        if ($customer->getLastName()) {
            $result['lastName'] = $customer->getLastName();
        }

        if ($customer->getCompanyName()) {
            $result['company'] = $customer->getCompanyName();
        }

        if ($customer->getEmail()) {
            $result['email'] = $customer->getEmail();
        }

        if ($customer->getContactPhone()) {
            $result['phone'] = $customer->getContactPhone();
        }

        if ($customer->getWebsite()) {
            $result['website'] = $customer->getWebsite();
        }

        if (count($options) > 0) {
            $result['options'] = $options;
        }

        return $result;
    }

    public function serializeCreditCard(CreditCard $creditCard, $options = array())
    {
        $result = array();

        if ($creditCard->getCustomer() && $creditCard->getCustomer()->getSubscriptionCustomerId()) {
            $result['customerId'] = $creditCard->getCustomer()->getSubscriptionCustomerId();
        }

        if ($creditCard->getNumber()) {
            $result['number'] = $creditCard->getNumber();
        }

        if ($creditCard->getCVV()) {
            $result['cvv'] = $creditCard->getCVV();
        }

        if ($creditCard->getExpirationDate()) {
            $result['expirationDate'] = $creditCard->getExpirationDate();
        }

        if ($creditCard->getCardholderName()) {
            $result['cardholderName'] = $creditCard->getCardholderName();
        }

        if (count($options) > 0) {
            $result['options'] = $options;
        }

        return $result;
    }

    public function serializeSubscription(Subscription $subscription, $options = array())
    {
        $result = array();

        if ($subscription->getPlan() && $subscription->getPlan()->getSubscriptionPlanId()) {
            $result['planId'] = $subscription->getPlan()->getSubscriptionPlanId();
        }

        if ($subscription->getCustomer() && count($subscription->getCustomer()->getCreditCards()) > 0) {
            $cards = $subscription->getCustomer()->getCreditCards();

            //set default credit card as default
            foreach($cards as $card) {
                if($card->isDefault())
                    $result['paymentMethodToken'] = $card->getToken();
            }
        }

        if (count($options) > 0) {
            $result['options'] = $options;
        }

        return $result;
    }
} 