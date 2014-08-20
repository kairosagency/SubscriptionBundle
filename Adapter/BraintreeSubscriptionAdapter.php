<?php

Namespace Kairos\SubscriptionBundle\Adapter;

use Doctrine\ORM\EntityManager;

use Kairos\SubscriptionBundle\KairosSubscriptionEvents;
use Kairos\SubscriptionBundle\Model\CreditCardInterface;
use Kairos\SubscriptionBundle\Model\PaymentInterface;
use Kairos\SubscriptionBundle\Model\Subscription;
use Kairos\SubscriptionBundle\Model\Transaction;
use Kairos\SubscriptionBundle\Model\CustomerInterface;
use Kairos\SubscriptionBundle\Model\SubscriptionInterface;
use Kairos\SubscriptionBundle\Model\PlanInterface;
use Kairos\SubscriptionBundle\Utils\Util;

use Braintree_Configuration,
    Braintree_Exception,
    Braintree_Customer,
    Braintree_CreditCard,
    Braintree_PaymentMethod,
    Braintree_Subscription,
    Braintree_Transaction,
    Braintree_WebhookNotification;

use Psr\Log\LoggerInterface\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;


class BraintreeSubscriptionAdapter implements SubscriptionAdapterInterface
{
    CONST ADAPTER_NAME = 'braintree';

    /**
     * @var string
     */
    protected $transactionClass;

    /**
     * @var \Psr\Log\LoggerInterface\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, $environment, $merchantId, $publicKey, $privateKey, $transactionClass)
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
     * @param PlanInterface $plan
     * @param array $options
     * @return PlanInterface
     */
    public function createPlan(PlanInterface $plan, $options = array())
    {
        $plan->setSubscriptionSynced(true);
        return $plan;
    }

    /**
     * @param PlanInterface $plan
     * @param array $options
     * @return PlanInterface
     */
    public function getPlan(PlanInterface $plan, $options = array())
    {
        return $plan;
    }

    /**
     * @param PlanInterface $plan
     * @param array $options
     * @return PlanInterface
     */
    public function updatePlan(PlanInterface $plan, $options = array())
    {
        $plan->setSubscriptionSynced(true);
        return $plan;
    }

    public function deletePlan(PlanInterface $plan, $options = array())
    {

    }



    /****** customer adapter ********/

    /**
     * @param CustomerInterface $customer
     * @param array $options
     * @return CustomerInterface
     */
    public function createCustomer(CustomerInterface $customer, $options = array())
    {
        try {
            $result = Braintree_Customer::create(
                $this->serializeCustomer($customer, $options)
            );

            if ($result->success) {
                $customer
                    ->setSubscriptionCustomerId($result->customer->id)
                    ->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][createCustomer] Sucess',
                    $this->serializeCustomer($customer, $options)
                );
            }
            else {
                $this->getLogger()->Error('[Braintree][createCustomer] Error', Util::braintreeErrorsToArray($result->errors->deepAll()));
                $customer->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createCustomer] ' . $e->getMessage());
        }

        return $customer;
    }

    /**
     * @param CustomerInterface $customer
     * @param array $options
     * @return object
     */
    public function getCustomer(CustomerInterface $customer, $options = array())
    {
        return Braintree_Customer::find($customer->getSubscriptionCustomerId());
    }

    /**
     * @param CustomerInterface $customer
     * @param array $options
     * @return CustomerInterface
     */
    public function updateCustomer(CustomerInterface $customer, $options = array())
    {
        try {
            $result = Braintree_Customer::update(
                $customer->getSubscriptionCustomerId(),
                $this->serializeCustomer($customer, $options)
            );

            if ($result->success) {
                $customer->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][createCustomer] Sucess',
                    array_merge(array('customer id' => $customer->getId()), $this->serializeCustomer($customer, $options))
                );
            }
            else {
                $this->getLogger()->Error('[Braintree][createCustomer] Error',
                    array_merge(array('customer id' => $customer->getId()), Util::braintreeErrorsToArray($result->errors->deepAll()))
                );
                $customer->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][updateCustomer] ' . $e->getMessage());
        }


        return $customer;
    }

    /**
     * @param CustomerInterface $customer
     * @param array $options
     * @return CustomerInterface
     */
    public function deleteCustomer(CustomerInterface $customer, $options = array())
    {
        try {
            $result = Braintree_Customer::delete($customer->getSubscriptionCustomerId());

            if ($result->success) {
                $customer->setSubscriptionSynced(false)->setSubscriptionCustomerId(null);
                $this->getLogger()->info('[Braintree][deleteCustomer] Sucess',
                    array('customer id' => $customer->getId())
                );
            }
            else {
                $this->getLogger()->Error('[Braintree][deleteCustomer] Error',
                    array_merge(array('customer id' => $customer->getId()), Util::braintreeErrorsToArray($result->errors->deepAll()))
                );
                $customer->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][deleteCustomer] ' . $e->getMessage());
        }

        return $customer;
    }


    /****** credit card ********/

    /**
     * @param CreditCardInterface $creditCard
     * @param array $options
     * @return CreditCardInterface
     */
    public function createCreditCard(CreditCardInterface $creditCard, $options = array())
    {
        try {
            $result = Braintree_CreditCard::create(
                $this->serializeCreditCard($creditCard, $options)
            );

            if ($result->success) {
                $creditCard->setToken($result->creditCard->token);
                $creditCard->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][createCreditCard] Sucess', $this->serializeCreditCard($creditCard, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][createCreditCard] Error', Util::braintreeErrorsToArray($result->errors->deepAll()));
                $creditCard->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createCreditCard] ' . $e->getMessage());
        }
        return $creditCard;
    }

    /****** payments ********/

    /**
     * @param PaymentInterface $creditCard
     * @param array $options
     * @return PaymentInterface
     */
    public function createPayment(PaymentInterface $payment, $options = array())
    {
        try {
            $result = Braintree_PaymentMethod::create(
                $this->serializePayment($payment, $options)
            );

            if ($result->success) {
                $payment->setSubscriptionSynced(true)
                    ->setToken($result->paymentMethod->token)
                    ->setExpirationDate($result->paymentMethod->expirationDate)
                    ->setMaskedNumber($result->paymentMethod->maskedNumber);

                $this->getLogger()->info('[Braintree][createPayment] Sucess', $this->serializePayment($payment, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][createPayment] Error', Util::braintreeErrorsToArray($result->errors->deepAll()));
                $payment->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createPayment] ' . $e->getMessage());
        }

        return $payment;
    }

    /**
     * @param PaymentInterface $creditCard
     * @param array $options
     * @return object
     */
    public function getPayment(PaymentInterface $creditCard, $options = array())
    {
        return Braintree_CreditCard::find($creditCard->getToken());
    }

    /**
     * @param PaymentInterface $creditCard
     * @param array $options
     * @return PaymentInterface
     */
    public function updatePayment(PaymentInterface $creditCard, $options = array())
    {
        try {
            $result = Braintree_CreditCard::update(
                $creditCard->getToken(),
                $this->serializeCreditCard($creditCard, $options)
            );

            if ($result->success) {
                $creditCard->setSubscriptionSynced(true);
                $this->getLogger()->info('[Braintree][updateCreditCard] Sucess',
                    array_merge(array('creditcard id' => $creditCard->getId()), $this->serializeCreditCard($creditCard, $options))
                );
            }
            else {
                $this->getLogger()->error('[Braintree][updateCreditCard] Error',
                    array_merge(array('creditcard id' => $creditCard->getId()), Util::braintreeErrorsToArray($result->errors->deepAll()))
                );
                $creditCard->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][updateCreditCard] ' . $e->getMessage());
        }

        return $creditCard;
    }

    public function deletePayment(PaymentInterface $payment, $options = array())
    {
        try {
            $result = Braintree_PaymentMethod::delete($payment->getToken());

            if ($result->success) {
                $payment->setSubscriptionSynced(false)->setToken(null);
                $this->getLogger()->info('[Braintree][deletePaymentMethod] Sucess',
                    array('payment id' => $payment->getId())
                );
            }
            else {
                $this->getLogger()->Error('[Braintree][deletePaymentMethod] Error',
                    array_merge(array('payment id' => $payment->getId()), Util::braintreeErrorsToArray($result->errors->deepAll()))
                );
                $payment->setErrors($result->errors->deepAll());
            }
        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][deletePaymentMethod] ' . $e->getMessage());
        }

        return $payment;

    }

    /****** Subscriptions ********/

    /**
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return SubscriptionInterface
     */
    public function createSubscription(SubscriptionInterface $subscription, $options = array())
    {
        try {
            $result = Braintree_Subscription::create(
                $this->serializeSubscription($subscription, $options)
            );

            if($result->success) {
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
                $this->getLogger()->info('[Braintree][createSubscription] Success', $this->serializeSubscription($subscription, $options));
            }
            else {
                $this->getLogger()->error('[Braintree][createSubscription] Error', Util::braintreeErrorsToArray($result->errors->deepAll()));
                $subscription->setErrors($result->errors->deepAll());
            }

        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][createSubscription] ' . $e->getMessage());
        }

        return $subscription;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return Braintree_Subscription
     */
    public function getSubscription(SubscriptionInterface $subscription, $options = array())
    {
        return Braintree_Subscription::find($subscription->getSubscriptionId());
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return SubscriptionInterface
     */
    public function updateSubscription(SubscriptionInterface $subscription, $options = array())
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
                $this->getLogger()->info('[Braintree][updateSubscription] Sucess',
                    array_merge(array('subscription id' => $subscription->getId()), $this->serializeSubscription($subscription, $options))
                );
            }
            else {
                $this->getLogger()->error('[Braintree][updateSubscription] Error',
                    array_merge(array('subscription id' => $subscription->getId()), Util::braintreeErrorsToArray($result->errors->deepAll()))
                );
                $subscription->setErrors($result->errors->deepAll());
            }


        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][updateSubscription] ' . $e->getMessage());
        }

        return $subscription;
    }

    /**
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return SubscriptionInterface
     */
    public function cancelSubscription(SubscriptionInterface $subscription, $options = array())
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
                $this->getLogger()->info('[Braintree][updateSubscription] Sucess',
                    array_merge(array('subscription id' => $subscription->getId()), $this->serializeSubscription($subscription, $options))
                );
            }
            else {
                $this->getLogger()->error('[Braintree][updateSubscription] Error',
                    array_merge(array('subscription id' => $subscription->getId()), Util::braintreeErrorsToArray($result->errors->deepAll()))
                );
                $subscription->setErrors($result->errors->deepAll());
            }


        } catch (Braintree_Exception $e) {
            $this->getLogger()->error('[Braintree exception][cancelSubscription] ' . $e->getMessage());
        }

        return $subscription;
    }


    /**
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return SubscriptionInterface
     */
    public function retryCharge(SubscriptionInterface $subscription, $options = array())
    {
        $retryResult = Braintree_Subscription::retryCharge(
            $subscription->getSubscriptionId()
        );

        if ($retryResult->success) {
            $result = Braintree_Transaction::submitForSettlement(
                $retryResult->transaction->id
            );

            // to check second result variable
            $transactionRefl = new \ReflectionClass($this->transactionClass);
            $transaction = $transactionRefl->newInstance();
            $transaction
                ->setSubscriptionTransactionId($result->id)
                ->setSubscriptionTransactionStatus($result->status);
        }

        return $subscription;
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
    public function getSubscriptionEvent(SubscriptionInterface $subscription, $notification)
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

    /**
     * @param string $challenge
     * @return string
     */
    public function verifyWebhook($challenge)
    {
        return Braintree_WebhookNotification::verify($challenge);
    }

    /**** serialization helper ****/

    /**
     * @param CustomerInterface $customer
     * @param array $options
     * @return array
     */
    public function serializeCustomer(CustomerInterface $customer, $options = array())
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

    /**
     * @param PaymentInterface $payment
     * @param array $options
     * @return array
     */
    public function serializePayment(PaymentInterface $payment, $options = array())
    {
        $result = array();

        if ($payment->getCustomer() && $payment->getCustomer()->getSubscriptionCustomerId()) {
            $result['customerId'] = $payment->getCustomer()->getSubscriptionCustomerId();
        }

        if ($payment->getNonce()) {
            $result['paymentMethodNonce'] = $payment->getNonce();
        }

        if (count($options) > 0) {
            $result['options'] = $options;
        }

        return $result;
    }

    /**
     * @param CreditCardInterface $creditCard
     * @param array $options
     * @return array
     */
    public function serializeCreditCard(CreditCardInterface $creditCard, $options = array())
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

    /**
     * @param SubscriptionInterface $subscription
     * @param array $options
     * @return array
     */
    public function serializeSubscription(SubscriptionInterface $subscription, $options = array())
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