<?php

namespace Rz\ClassificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //override Context Admin
        $definition = $container->getDefinition('sonata.classification.admin.context');
        $definition->setClass($container->getParameter('sonata.classification.admin.context.class'));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
            $container->getParameter('rz_classification.configuration.context.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));

        //override Category Admin
        $definition = $container->getDefinition('sonata.classification.admin.category');
        $definition->setClass($container->getParameter('sonata.classification.admin.category.class'));
        $definition->addMethodCall('setCategoryManager', array($container->getDefinition('sonata.classification.manager.category')));
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


        //override ORM Manager
        $definition = $container->getDefinition('sonata.classification.manager.tag');
        $definition->setClass($container->getParameter('rz.classification.manager.tag.class'));

        $definition = $container->getDefinition('sonata.classification.manager.category');
        $definition->setClass($container->getParameter('rz.classification.manager.category.class'));
        $definition->addMethodCall('setPermalinkGenerator', array(new Reference('rz_classificaiton.permalink.category')));

        $definition = $container->getDefinition('sonata.classification.manager.collection');
        $definition->setClass($container->getParameter('rz.classification.manager.collection.class'));

        $definition = $container->getDefinition('sonata.classification.manager.context');
        $definition->setClass($container->getParameter('rz.classification.manager.context.class'));
    }
}
