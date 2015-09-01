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


        #####################################
        ## Override Category Admin
        #####################################
        $definition = $container->getDefinition('sonata.classification.admin.category');
        $definition->setClass($container->getParameter('sonata.classification.admin.category.class'));
        $definition->addMethodCall('setCategoryManager', array($container->getDefinition('sonata.classification.manager.category')));
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_classification.configuration.category.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));
        $definition->addMethodCall('setSlugGenerator', array(new Reference('rz_classification.slug_generator')));
        $definition->addMethodCall('setControllerEnabled', array($container->getParameter('rz_classification.enable_controllers')));
        if (interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            $definition->addMethodCall('setPageManager', array(new Reference('sonata.page.manager.page')));
            $definition->addMethodCall('setSiteManager', array(new Reference('sonata.page.manager.site')));
            $definition->addMethodCall('setDefaultPageTemplate', array($container->getParameter('rz_classification.settings.category.default_category_page_template')));
        }
        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $definition->addMethodCall('setMediaManager', array(new Reference('sonata.media.manager.media')));
        }
        $definition->addMethodCall('setPool', array(new Reference('rz_classification.pool.category')));


        #####################################
        ## Override Tag Admin
        #####################################
        $definition = $container->getDefinition('sonata.classification.admin.tag');
        $definition->setClass($container->getParameter('sonata.classification.admin.tag.class'));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_classification.configuration.tag.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setPool', array(new Reference('rz_classification.pool.tag')));


        #####################################
        ## Override Collection Admin
        #####################################
        $definition = $container->getDefinition('sonata.classification.admin.collection');
        $definition->setClass($container->getParameter('sonata.classification.admin.collection.class'));
        $definedTemplates = array_merge($container->getParameter('sonata.admin.configuration.templates'),
                                        $container->getParameter('rz_classification.configuration.collection.templates'));
        $definition->addMethodCall('setTemplates', array($definedTemplates));
        $definition->addMethodCall('setContextManager', array(new Reference('sonata.classification.manager.context')));
        $definition->addMethodCall('setSlugGenerator', array(new Reference('rz_classification.slug_generator')));
        $definition->addMethodCall('setControllerEnabled', array($container->getParameter('rz_classification.enable_controllers')));

        if (interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            $definition->addMethodCall('setPageManager', array(new Reference('sonata.page.manager.page')));
            $definition->addMethodCall('setSiteManager', array(new Reference('sonata.page.manager.site')));
        }
        if (interface_exists('Sonata\MediaBundle\Model\MediaInterface')) {
            $definition->addMethodCall('setMediaManager', array(new Reference('sonata.media.manager.media')));
        }

        $definition->addMethodCall('setPool', array(new Reference('rz_classification.pool.collection')));

        #####################################
        ## Override ORM Manager
        #####################################
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
