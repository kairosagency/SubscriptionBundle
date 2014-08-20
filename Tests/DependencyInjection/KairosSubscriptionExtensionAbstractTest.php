<?php

namespace Kairos\SubscriptionBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;

use Kairos\SubscriptionBundle\DependencyInjection\KairosSubscriptionExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
Abstract class KairosSubscriptionExtensionAbstractTest extends \PHPUnit_Framework_TestCase
{

    private $extension;
    private $container;

    protected function setUp()
    {
        $this->extension = new KairosSubscriptionExtension();

        $this->container = new ContainerBuilder();
        $this->container->registerExtension($this->extension);

        //$this->container->register('event_dispatcher', new EventDispatcher());

    }

    /**
     * @param ContainerBuilder $container
     * @param string $resource
     * @return mixed
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $resource);


    /*public function testConfiguration()
    {
        $this->loadConfiguration($this->container, 'config');
        $this->container->compile();
        $this->assertEquals('Kairos\SubscriptionBundle\Entity\Transaction',$this->container->getParameter('kairos_subscription.transaction.class'));
        $this->assertEquals('Kairos\SubscriptionBundle\Entity\Customer',$this->container->getParameter('kairos_subscription.customer.class'));
        $this->assertEquals('Kairos\SubscriptionBundle\Entity\Plan',$this->container->getParameter('kairos_subscription.plan.class'));
        $this->assertEquals('Kairos\SubscriptionBundle\Entity\Subscription',$this->container->getParameter('kairos_subscription.subscription.class'));
        $this->assertEquals('Kairos\SubscriptionBundle\Entity\CreditCard',$this->container->getParameter('kairos_subscription.credit_card.class'));

        $this->assertEquals('braintree',$this->container->getParameter('kairos_subscription.adapter_name'));
    }*/

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testBadConfiguration()
    {
        // should throw an exception since some parameters are missing
        $this->loadConfiguration($this->container, 'bad_config');
        $this->container->compile();
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testWithoutConfiguration()
    {
        // should throw an exception since some parameters are missing
        $this->container->loadFromExtension($this->extension->getAlias());
        $this->container->compile();
    }
}