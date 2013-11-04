<?php

namespace Rz\ClassificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //override Category Admin
        $definition = $container->getDefinition('sonata.classification.admin.category');
        $definition->setClass($container->getParameter('sonata.classification.admin.category.class'));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_classification.configuration.category.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        //override Tag Admin
        $definition = $container->getDefinition('sonata.classification.admin.tag');
        $definition->setClass($container->getParameter('sonata.classification.admin.tag.class'));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_classification.configuration.tag.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        //override Collection Admin
        $definition = $container->getDefinition('sonata.classification.admin.collection');
        $definition->setClass($container->getParameter('sonata.classification.admin.collection.class'));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_classification.configuration.collection.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));
    }
}
