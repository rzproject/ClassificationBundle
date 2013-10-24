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
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('form.xml');
        $this->configureClass($config, $container);
        $this->configureAdmin($config, $container);
        $this->configureRzTemplates($config, $container);
    }

        /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureClass($config, ContainerBuilder $container)
    {
        // admin configuration
        $container->setParameter('sonata.classification.admin.tag.entity',        $config['class']['tag']);
        $container->setParameter('sonata.classification.admin.category.entity',   $config['class']['category']);
        $container->setParameter('sonata.classification.admin.collection.entity', $config['class']['collection']);

        // manager configuration
        $container->setParameter('sonata.classification.manager.tag.entity',        $config['class']['tag']);
        $container->setParameter('sonata.classification.manager.category.entity',   $config['class']['category']);
        $container->setParameter('sonata.classification.manager.collection.entity', $config['class']['collection']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureAdmin($config, ContainerBuilder $container)
    {
        $container->setParameter('sonata.classification.admin.category.class',                $config['admin']['category']['class']);
        $container->setParameter('sonata.classification.admin.category.controller',           $config['admin']['category']['controller']);
        $container->setParameter('sonata.classification.admin.category.translation_domain',   $config['admin']['category']['translation']);

        $container->setParameter('sonata.classification.admin.tag.class',                     $config['admin']['tag']['class']);
        $container->setParameter('sonata.classification.admin.tag.controller',                $config['admin']['tag']['controller']);
        $container->setParameter('sonata.classification.admin.tag.translation_domain',        $config['admin']['tag']['translation']);

        $container->setParameter('sonata.classification.admin.collection.class',              $config['admin']['collection']['class']);
        $container->setParameter('sonata.classification.admin.collection.controller',         $config['admin']['collection']['controller']);
        $container->setParameter('sonata.classification.admin.collection.translation_domain', $config['admin']['collection']['translation']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureRzTemplates($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_classification.configuration.category.templates', $config['admin']['category']['templates']);
        $container->setParameter('rz_classification.configuration.tag.templates', $config['admin']['tag']['templates']);
        $container->setParameter('rz_classification.configuration.collection.templates', $config['admin']['collection']['templates']);
    }
}
