<?php

namespace Kairos\SubscriptionBundle\Tests\DependencyInjection;

use Kairos\SubscriptionBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;


class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $config = Yaml::parse(file_get_contents(__DIR__.'/ConfigFixtures/config.yml'));

        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();
        $processor = new Processor;

        $config = $processor->process($treeBuilder->buildTree(), $config);
        $this->assertNotNull($config);
    }
}