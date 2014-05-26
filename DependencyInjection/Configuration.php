<?php

namespace Kairos\SubscriptionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kairos_subscription');

        //$supportedDrivers = array('orm', 'mongodb', 'couchdb', 'propel', 'custom');
        $supportedDrivers = array('orm');

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->defaultValue('orm')
                    ->validate()
                        ->ifNotInArray($supportedDrivers)
                        ->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode($supportedDrivers))
                    ->end()
                    ->cannotBeOverwritten()
                ->end()

                ->scalarNode('model_manager_name')->defaultNull()->end()

                ->arrayNode('classes')
                    ->children()
                        ->scalarNode('customer')->end()
                        ->scalarNode('plan')->end()
                        ->scalarNode('credit_card')->end()
                        ->scalarNode('subscription')->end()
                        ->scalarNode('transaction')->end()
                    ->end()
                ->end()
            ->end();


        $this->addAdapterSection($rootNode);

        return $treeBuilder;
    }

    public function addAdapterSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('adapter')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->arrayNode('braintree')
                            ->children()

                                ->scalarNode('environment')
                                    ->validate()
                                        ->ifNotInArray(array('sandbox'))
                                        ->thenInvalid('The environment %s is not supported. Please choose one of '.json_encode(array('sandbox')))
                                    ->end()
                                    ->defaultValue('sandbox')
                                ->end()

                                ->scalarNode('merchant_id')
                                    ->isRequired()->cannotBeEmpty()->end()

                                ->scalarNode('public_key')
                                    ->isRequired()->cannotBeEmpty()->end()

                                ->scalarNode('private_key')
                                    ->isRequired()->cannotBeEmpty()->end()

                                ->scalarNode('client_side_encryption_key')
                                    ->isRequired()->cannotBeEmpty()->end()

                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
