<?php

namespace Rz\ClassificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddProviderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->attachProviders($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function attachProviders(ContainerBuilder $container)
    {
        ########################
        # Category Provider
        ########################
        $pool = $container->getDefinition('rz.classification.category.pool');

        foreach ($container->findTaggedServiceIds('rz.classification.category.provider') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('rz.classification.category.provider.context');

        foreach ($contexts as $name => $settings) {
            $pool->addMethodCall('addContext', array($name, $settings['provider']));
        }

        ########################
        # Collection Provider
        ########################
        $pool = $container->getDefinition('rz.classification.collection.pool');

        foreach ($container->findTaggedServiceIds('rz.classification.collection.provider') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('rz.classification.collection.provider.context');

        foreach ($contexts as $name => $settings) {
            $pool->addMethodCall('addContext', array($name, $settings['provider']));
        }

        ########################
        # Tag Provider
        ########################
        $pool = $container->getDefinition('rz.classification.tag.pool');

        foreach ($container->findTaggedServiceIds('rz.classification.tag.provider') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('rz.classification.tag.provider.context');

        foreach ($contexts as $name => $settings) {
            $pool->addMethodCall('addContext', array($name, $settings['provider']));
        }
    }
}
