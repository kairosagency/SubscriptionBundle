<?php
namespace Kairos\SubscriptionBundle\Tests\Adapter;

use Kairos\SubscriptionBundle\Adapter\BraintreeSubscriptionAdapter;
use Kairos\SubscriptionBundle\Model\Subscription;
use Kairos\SubscriptionBundle\Tests\TestEntities;


class BraintreeSubscriptionAdapterTest extends \PHPUnit_Framework_TestCase {


    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var BraintreeSubscriptionAdapter
     */
    private $adapter;

    /**
     * @var \Kairos\SubscriptionBundle\Tests\TestEntities\CustomerEntity
     */
    private $customer;

    protected function setUp()
    {
        $this->logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface', array('info', 'error'));

        $this->logger->expects($this->any())
            ->method('info');

        $this->logger->expects($this->any())
            ->method('error');

        $this->adapter = new BraintreeSubscriptionAdapter(
            $this->logger,
            'sandbox',
            MERCHANTID,
            PUBLICKEY,
            PRIVATEKEY,
            'Kairos\SubscriptionBundle\Tests\TestEntities\TransactionEntity'
        );

    }


    public function testGetAdapterName()
    {
        $this->assertEquals('braintree', $this->adapter->getAdapterName());
    }


    public function testCreatePlan()
    {
        $plan = new TestEntities\PlanEntity();
        $this->assertTrue($this->adapter->createPlan($plan)->isSubscriptionSynced());
    }

    public function testGetPlan()
    {
        $plan = new TestEntities\PlanEntity();
        $this->assertEquals($plan, $this->adapter->getPlan($plan));
    }

    public function testUpdatePlan()
    {
        $plan = new TestEntities\PlanEntity();
        $this->assertTrue($this->adapter->updatePlan($plan)->isSubscriptionSynced());
    }

    public function testFailCreateUser()
    {
        $customer = new TestEntities\CustomerEntity();
        $customer->setEmail('alibaba');
        $this->assertFalse($this->adapter->createCustomer($customer)->isSubscriptionSynced());
        $this->assertNull($customer->getSubscriptionCustomerId());
    }

    public function testCreateUser()
    {
        $customer = new TestEntities\CustomerEntity();
        $this->assertTrue($this->adapter->createCustomer($customer)->isSubscriptionSynced());
        $this->assertNotNull($customer->getSubscriptionCustomerId());
        return $customer;
    }

    /**
     * @depends testCreateUser
     */
    public function testUpdateUser($customer)
    {
        $customer->setFirstName('Alo'.rand(0,1000))->setLastName('Boby'.rand(0,1000));
        $this->assertTrue($this->adapter->updateCustomer($customer)->isSubscriptionSynced());
        return $customer;
    }

    /**
     * @depends testCreateUser
     */
    public function testCreatePayment($customer)
    {
        $payment = new TestEntities\CreditCardEntity();
        $payment->setCustomer($customer);
        $payment->setNonce(\Braintree_Test_Nonces::$transactable);
        $this->adapter->createPayment($payment);

        $this->assertTrue($payment->isSubscriptionSynced());
        $this->assertNotNull($payment->getToken());
        $this->assertRegExp('/[0-9*]*/', $payment->getMaskedNumber());
        $this->assertRegExp('/[0-9][0-9]\/[0-9][0-9]/', $payment->getExpirationDate());

        return $payment;
    }


    /**
     * @depends testCreateUser
     * @depends testCreatePayment
     */
    public function testCreateSubscription($customer, $payment)
    {
        $plan = new TestEntities\PlanEntity();
        $subscription = new TestEntities\SubscriptionEntity();

        $customer->addPayment($payment);
        $subscription->setCustomer($customer);
        $subscription->setPlan($plan);
        $subscription = $this->adapter->createSubscription($subscription);

        $this->assertTrue($subscription->isSubscriptionSynced());
        $this->assertNotNull($subscription->getSubscriptionId());

        return $subscription;
    }

    /**
     * @depends testCreateSubscription
     */
    public function testDeleteSubscription($subscription)
    {
        $subscription = $this->adapter->deleteSubscription($subscription);
        $this->assertFalse($subscription->isSubscriptionSynced());
        $this->assertEquals(Subscription::CANCELED, $subscription->getStatus());
        return $subscription;
    }


    /**
     * @depends testCreatePayment
     */
    public function testDeletePayment($payment)
    {
        $payment = $this->adapter->deletePayment($payment);
        $this->assertFalse($payment->isSubscriptionSynced());
        $this->assertNull($payment->getToken());
        return $payment;
    }


    /**
     * @depends testUpdateUser
     */
    public function testDeleteUser($customer)
    {
        $customer = $this->adapter->deleteCustomer($customer);
        $this->assertFalse($customer->isSubscriptionSynced());
        $this->assertNull($customer->getSubscriptionCustomerId());
        return $customer;
    }
}
 