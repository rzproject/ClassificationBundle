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

        $contexts = $container->getParameter('rz_classification.provider.category.context');
        foreach ($contexts as $name => $settings) {
            $templates = array();

            foreach ($settings['templates'] as $template => $value) {
                $templates[$template] = $value;
            }
            $pool->addMethodCall('addContext', array($name, $settings['provider'], $settings['default_template'], $templates));

            if ($container->hasDefinition($settings['provider'])) {
                $provider = $container->getDefinition($settings['provider']);
                $provider->addMethodCall('setTemplates', array($templates));
                $provider->addMethodCall('setMediaAdmin', array(new Reference('sonata.media.admin.media')));
                $provider->addMethodCall('setMediaManager', array(new Reference('sonata.media.manager.media')));
                $provider->addMethodCall('setMetatagChoices', array($container->getParameter('rz_seo.metatags')));
            }
        }


        //collection
        $pool = $container->getDefinition('rz_classification.pool.collection');
        foreach ($container->findTaggedServiceIds('rz_classification.provider.collection') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('rz_classification.provider.collection.context');
        foreach ($contexts as $name => $settings) {
            $templates = array();

            foreach ($settings['templates'] as $template => $value) {
                $templates[$template] = $value;
            }
            $pool->addMethodCall('addContext', array($name, $settings['provider'], $settings['default_template'], $templates));

            if ($container->hasDefinition($settings['provider'])) {
                $provider = $container->getDefinition($settings['provider']);
                $provider->addMethodCall('setTemplates', array($templates));
                $provider->addMethodCall('setMediaAdmin', array(new Reference('sonata.media.admin.media')));
                $provider->addMethodCall('setMediaManager', array(new Reference('sonata.media.manager.media')));
                $provider->addMethodCall('setMetatagChoices', array($container->getParameter('rz_seo.metatags')));
            }
        }

        //tag
        $pool = $container->getDefinition('rz_classification.pool.tag');
        foreach ($container->findTaggedServiceIds('rz_classification.provider.tag') as $id => $attributes) {
            $pool->addMethodCall('addProvider', array($id, new Reference($id)));
        }

        $contexts = $container->getParameter('rz_classification.provider.tag.context');
        foreach ($contexts as $name => $settings) {
            $templates = array();

            foreach ($settings['templates'] as $template => $value) {
                $templates[$template] = $value;
            }
            $pool->addMethodCall('addContext', array($name, $settings['provider'], $settings['default_template'], $templates));

            if ($container->hasDefinition($settings['provider'])) {
                $provider = $container->getDefinition($settings['provider']);
                $provider->addMethodCall('setTemplates', array($templates));
                $provider->addMethodCall('setMediaAdmin', array(new Reference('sonata.media.admin.media')));
                $provider->addMethodCall('setMediaManager', array(new Reference('sonata.media.manager.media')));
                $provider->addMethodCall('setMetatagChoices', array($container->getParameter('rz_seo.metatags')));
            }
        }
    }
}
