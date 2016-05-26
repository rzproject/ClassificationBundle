<?php

namespace Rz\ClassificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RzClassificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('twig.xml');
        $this->configureManagerClass($config, $container);
        $this->configureSettings($config, $container);
        $loader->load('provider.xml');
        $this->configureProviders($config['providers'], $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureSettings($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.classification.slugify_service', $config['slugify_service']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.classification.entity.manager.tag.class',        $config['manager_class']['orm']['tag']);
        $container->setParameter('rz.classification.entity.manager.category.class',   $config['manager_class']['orm']['category']);
        $container->setParameter('rz.classification.entity.manager.collection.class', $config['manager_class']['orm']['collection']);
        $container->setParameter('rz.classification.entity.manager.context.class',    $config['manager_class']['orm']['context']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureProviders($config, ContainerBuilder $container)
    {
        #Category Provider
        $categoryPool = $container->getDefinition('rz.classification.category.pool');
        $categoryPool->replaceArgument(0, $config['category']['default_provider_context']);

        $container->setParameter('rz.classification.category.default_context',                    $config['category']['default_context']);
        $container->setParameter('rz.classification.category.provider.default_provider_context',  $config['category']['default_provider_context']);
        $container->setParameter('rz.classification.category.provider.context',                   $config['category']['context']);


        #Collection Provider
        $collectionPool = $container->getDefinition('rz.classification.collection.pool');
        $collectionPool->replaceArgument(0, $config['collection']['default_provider_context']);

        $container->setParameter('rz.classification.collection.default_context',                    $config['collection']['default_context']);
        $container->setParameter('rz.classification.collection.provider.default_provider_context',  $config['collection']['default_provider_context']);
        $container->setParameter('rz.classification.collection.provider.context',                   $config['collection']['context']);

        #Tag Provider
        $collectionPool = $container->getDefinition('rz.classification.tag.pool');
        $collectionPool->replaceArgument(0, $config['tag']['default_provider_context']);

        $container->setParameter('rz.classification.tag.default_context',                    $config['tag']['default_context']);
        $container->setParameter('rz.classification.tag.provider.default_provider_context',  $config['tag']['default_provider_context']);
        $container->setParameter('rz.classification.tag.provider.context',                   $config['tag']['context']);

    }
}
