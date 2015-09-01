<?php

namespace Rz\ClassificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

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
        $loader->load('form.xml');
        $loader->load('permalink.xml');
        $loader->load('twig.xml');
        $loader->load('provider.xml');
        $loader->load('slug_generator.xml');
        $this->configureClass($config, $container);
        $this->registerDoctrineMapping($config, $container);
        $this->configureManagerClass($config, $container);
        $this->configureAdmin($config, $container);
        $this->configureRzTemplates($config, $container);
        $this->configureSettings($container, $config);
        $this->configureCategoryProviders($container, $config['providers']['category']);
        $this->configureCollectionProviders($container, $config['providers']['collection']);
        $this->configureTagProviders($container, $config['providers']['tag']);

        if (interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            $loader->load('block.xml');
            $this->configureBlocks($config['blocks'], $container);
        }

        $this->configureClassesToCompile();
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureSettings(ContainerBuilder $container, $config) {
        $container->setParameter('rz_classification.enable_controllers', $config['enable_controllers']);
        $container->setParameter('rz_classification.settings.category.enable_category_canonical_page', $config['settings']['category']['enable_category_canonical_page']);
        $container->setParameter('rz_classification.settings.category.parent_category_page_template', $config['settings']['category']['parent_category_page_template']);
        $container->setParameter('rz_classification.settings.category.default_category_page_template', $config['settings']['category']['default_category_page_template']);
        $container->setParameter('rz_classification.settings.category.category_list_max_per_page', $config['settings']['category']['category_list_max_per_page']);
        $container->setParameter('rz_classification.settings.tag.enable_tag_canonical_page', $config['settings']['tag']['enable_tag_canonical_page']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureCategoryProviders(ContainerBuilder $container, $config) {

        //category
        $pool = $container->getDefinition('rz_classification.pool.category');
        $pool->replaceArgument(0, $config['default_context']);
        $container->setParameter('rz_classification.category.default_context', $config['default_context']);
        $container->setParameter('rz_classification.provider.category.context', $config['contexts']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureCollectionProviders(ContainerBuilder $container, $config) {

        //collection
        $pool = $container->getDefinition('rz_classification.pool.collection');
        $pool->replaceArgument(0, $config['default_context']);
        $container->setParameter('rz_classification.collection.default_context', $config['default_context']);
        $container->setParameter('rz_classification.provider.collection.context', $config['contexts']);

    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $config
     */
    public function configureTagProviders(ContainerBuilder $container, $config)
    {
        //tag
        $pool = $container->getDefinition('rz_classification.pool.tag');
        $pool->replaceArgument(0, $config['default_context']);

        $container->setParameter('rz_classification.tag.default_context', $config['default_context']);
        $container->setParameter('rz_classification.provider.tag.context', $config['contexts']);
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
        $container->setParameter('sonata.classification.admin.context.entity', $config['class']['context']);

        // manager configuration
        $container->setParameter('sonata.classification.manager.tag.entity',        $config['class']['tag']);
        $container->setParameter('sonata.classification.manager.category.entity',   $config['class']['category']);
        $container->setParameter('sonata.classification.manager.collection.entity', $config['class']['collection']);
        $container->setParameter('sonata.classification.manager.context.entity', $config['class']['context']);

        $container->setParameter('rz_classification.slug_generator.class', $config['class']['slug_generator']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureManagerClass($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.classification.manager.tag.class',        $config['manager_class']['tag']);
        $container->setParameter('rz.classification.manager.category.class',   $config['manager_class']['category']);
        $container->setParameter('rz.classification.manager.collection.class', $config['manager_class']['collection']);
        $container->setParameter('rz.classification.manager.context.class',    $config['manager_class']['context']);


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

        $container->setParameter('sonata.classification.admin.context.class',              $config['admin']['context']['class']);
        $container->setParameter('sonata.classification.admin.context.controller',         $config['admin']['context']['controller']);
        $container->setParameter('sonata.classification.admin.context.translation_domain', $config['admin']['context']['translation']);
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
        $container->setParameter('rz_classification.configuration.context.templates', $config['admin']['context']['templates']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureBlocks($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_classification.block.category.class', $config['category']['class']);
        $container->setParameter('rz_classification.settings.category_pager_max_per_page', $config['category']['category_pager_max_per_page']);

        # template
        $temp = $config['category']['templates'];
        $templates = array();
        foreach ($temp as $template) {
            $templates[$template['path']] = $template['name'];
        }
        $container->setParameter('rz_classification.block.category.templates', $templates);

        # ajax template
        $ajaxTemp = $config['category']['ajax_templates'];
        $ajaxTemplates = array();
        foreach ($ajaxTemp as $ajaxTemplate) {
            $ajaxTemplates[$ajaxTemplate['path']] = $ajaxTemplate['name'];
        }
        $container->setParameter('rz_classification.block.category.ajax_templates', $ajaxTemplates);

        # ajax pager template
        $ajaxPagerTemp = $config['category']['ajax_pager_templates'];
        $ajaxPagerTemplates = array();
        foreach ($ajaxPagerTemp as $ajaxPagerTemplate) {
            $ajaxPagerTemplates[$ajaxPagerTemplate['path']] = $ajaxPagerTemplate['name'];
        }
        $container->setParameter('rz_classification.block.category.ajax_pager_templates', $ajaxPagerTemplates);

    }

    /**
     * @param array $config
     */
    public function registerDoctrineMapping(array $config)
    {

        foreach ($config['class'] as $type => $class) {
            if (!class_exists($class)) {
                return;
            }
        }

        $collector = DoctrineCollector::getInstance();


        if (interface_exists('Sonata\PageBundle\Model\PageInterface')) {
            $collector->addAssociation($config['class']['category'], 'mapManyToOne', array(
                'fieldName' => 'page',
                'targetEntity' => $config['class']['page'],
                'cascade' =>
                    array(
                        0 => 'persist',
                        1 => 'detach',
                    ),
                'mappedBy' => NULL,
                'inversedBy' => NULL,
                'joinColumns' =>
                    array(
                        array(
                            'name' => 'page_id',
                            'referencedColumnName' => 'id',
                        ),
                    ),
                'orphanRemoval' => false,
            ));
        }
    }

    /**
     * Add class to compile
     */
    public function configureClassesToCompile()
    {
        $this->addClassesToCompile(array(
            "Rz\\ClassificationBundle\\Block\\CategoryBlockService",
            "Rz\\ClassificationBundle\\Provider\\ClassificationProviderInterface",
            "Rz\\ClassificationBundle\\Provider\\CategoryProviderInterface",
            "Rz\\ClassificationBundle\\Provider\\TagProviderInterface",
            "Rz\\ClassificationBundle\\Provider\\BaseProvider",
            "Rz\\ClassificationBundle\\Provider\\BaseCategoryProvider",
            "Rz\\ClassificationBundle\\Provider\\BaseTagProvider",
            "Rz\\ClassificationBundle\\Provider\\CategoryDefaultProvider",
            "Rz\\ClassificationBundle\\Provider\\CategoryNewsProvider",
            "Rz\\ClassificationBundle\\Provider\\TagDefaultProvider",
            "Rz\\ClassificationBundle\\Provider\\TagNewsProvider",
            "Rz\\ClassificationBundle\\Provider\\Pool",
            "Rz\\ClassificationBundle\\Provider\\CategoryPool",
            "Rz\\ClassificationBundle\\Provider\\TagPool",
            "Rz\\ClassificationBundle\\Form\\Type\\CategorySelectorType",
            "Rz\\ClassificationBundle\\Permalink\\PermalinkInterface",
            "Rz\\ClassificationBundle\\Permalink\\CategoryPermalink",
            "Rz\\ClassificationBundle\\Twig\\Extension\\CategoryExtension",
        ));
    }
}
