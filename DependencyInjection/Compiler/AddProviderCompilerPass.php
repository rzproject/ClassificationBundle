<?php

namespace Rz\ClassificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

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

        //category
        $pool = $container->getDefinition('rz_classification.pool.category');
        foreach ($container->findTaggedServiceIds('rz_classification.provider.category') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        //collection
        $pool = $container->getDefinition('rz_classification.pool.collection');
        foreach ($container->findTaggedServiceIds('rz_classification.provider.collection') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        //tag
        $pool = $container->getDefinition('rz_classification.pool.tag');
        foreach ($container->findTaggedServiceIds('rz_classification.provider.tag') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }
    }
}
