<?php

namespace Kairos\SubscriptionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;


/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KairosSubscriptionExtension extends Extension
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        // load db driver config
        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
            $this->container->setParameter($this->getAlias() . '.backend_type_' . $config['db_driver'], true);
        }

        //load xml config files
        foreach (array('adapters', 'payment', 'twig') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        // remap config parameters to bundle parameters
        $this->remapParametersNamespaces($config, array(
                ''          => array(
                    'model_manager_name' => 'kairos_subscription.model_manager_name',
                ),
                'classes'   => array(
                    'transaction'       =>  'kairos_subscription.transaction.class',
                    'customer'          =>  'kairos_subscription.customer.class',
                    'plan'              =>  'kairos_subscription.plan.class',
                    'credit_card'       =>  'kairos_subscription.credit_card.class',
                    'subscription'      =>  'kairos_subscription.subscription.class',
                )
            ));

        // set subscription adapter from config
        $this->setSubscriptionAdapter($config);
    }


    protected function setSubscriptionAdapter($config)
    {
        // get adapter name from config values
        $adapter = $config['adapter'];
        reset($adapter);
        $adapter_name = key($adapter);

        // set adaoter name in container parameters (can be usefull !)
        $this->container->setParameter('kairos_subscription.adapter_name', $adapter_name);

        // if adapter is braintree
        // WARNING : parameters must be in the same order than in th BraintreeSubscription constructor
        if($adapter_name == 'braintree') {
            // register subscription adapter service
            $this->addAdapter($adapter_name,
                array(
                    $adapter[$adapter_name]['environment'],
                    $adapter[$adapter_name]['merchant_id'],
                    $adapter[$adapter_name]['public_key'],
                    $adapter[$adapter_name]['private_key'],
                    $this->container->getParameter('kairos_subscription.transaction.class'),
                )
            );

            // register twig js service
            $this->addTwigJsService($adapter_name,
                array(
                    $adapter[$adapter_name]['client_side_encryption_key'],
                )
            );
        }
    }


    /******** util functions ********/

    protected function remapParameters(array $config, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $this->container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $this->container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }

    protected function addAdapter($name, array $arguments = array())
    {
        $adapter = new Definition(
            '%kairos_subscription.adapter.'.$name.'.class%',
            array_merge(
                array(new Reference('logger')),
                $arguments
            )
        );

        $adapter
            ->setPublic(true);

        $this->container->setDefinition('kairos_subscription.subscription_adapter', $adapter);
    }

    protected function addTwigJsService($name, array $arguments = array())
    {
        $jsService = new Definition(
            '%kairos_subscription.twig.'.$name.'.class%',
            array_merge(
                array(new Reference('kairos_subscription.payment.form.type')),
                $arguments
            )
        );

        $jsService
            ->setPublic(true);

        $this->container->setDefinition('kairos_subscription.twig_js_service', $jsService);
    }
}
