<?php

namespace Kairos\SubscriptionBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class KairosSubscriptionExtensionTest extends KairosSubscriptionExtensionAbstractTest
{

    protected function loadConfiguration(ContainerBuilder $container, $resource)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/ConfigFixtures/'));
        $loader->load($resource.'.yml');
    }
}