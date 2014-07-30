<?php

/*
 * This file is part of the KairosSubscriptionBundle package.
 */

namespace Kairos\SubscriptionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Registers the additional validators according to the storage
 *
 * todo : finalize this later
 */
class ValidationPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('kairos_subscription.storage')) {
            return;
        }

        $storage = $container->getParameter('kairos_subscription.storage');

        if ('custom' === $storage) {
            return;
        }

        $validationFile = __DIR__ . '/../../Resources/config/validation/' . $storage . '.xml';

        if ($container->hasDefinition('validator.builder')) {
            // Symfony 2.5+
            $container->getDefinition('validator.builder')
                ->addMethodCall('addXmlMapping', array($validationFile));

            return;
        }

        // Old method of loading validation
        if (!$container->hasParameter('validator.mapping.loader.xml_files_loader.mapping_files')) {
            return;
        }

        $files = $container->getParameter('validator.mapping.loader.xml_files_loader.mapping_files');
        if (is_file($validationFile)) {
            $files[] = realpath($validationFile);
            $container->addResource(new FileResource($validationFile));
        }

        $container->setParameter('validator.mapping.loader.xml_files_loader.mapping_files', $files);
    }
}