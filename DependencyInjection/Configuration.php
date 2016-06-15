<?php

namespace Rz\ClassificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('rz_classification');
        $this->addSettingsSection($node);
        $this->addManagerSection($node);
        $this->addProviderSection($node);
        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('slugify_service')
                    ->info('You should use: sonata.core.slugify.cocur, but for BC we keep \'sonata.core.slugify.native\' as default')
                    ->defaultValue('sonata.core.slugify.cocur')
                ->end()
                ->arrayNode('settings')
                    ->cannotBeEmpty()
                    ->children()
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                            ->end()  #--end category children
                        ->end() #--end category
                        ->arrayNode('collection')
                            ->cannotBeEmpty()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                            ->end()  #--end collection children
                        ->end() #--end collection
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default_context')->cannotBeEmpty()->end()
                            ->end()  #--end tag children
                        ->end()#--end tag
                    ->end()#--end children settings
                ->end()#--end settings
            ->end()
        ;
    }

     /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addManagerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('manager_class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('tag')->defaultValue('Rz\\ClassificationBundle\\Entity\\TagManager')->end()
                                ->scalarNode('category')->defaultValue('Rz\\ClassificationBundle\\Entity\\CategoryManager')->end()
                                ->scalarNode('collection')->defaultValue('Rz\\ClassificationBundle\\Entity\\CollectionManager')->end()
                                ->scalarNode('context')->defaultValue('Rz\\ClassificationBundle\\Entity\\ContextManager')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addProviderSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('providers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('class')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('pool')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('category')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Provider\\Category\\Pool')->end()
                                        ->scalarNode('collection')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Provider\\Collection\\Pool')->end()
                                        ->scalarNode('tag')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Provider\\Tag\\Pool')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('default_provider')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('category')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Provider\\Category\\DefaultProvider')->end()
                                        ->scalarNode('collection')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Provider\\Collection\\DefaultProvider')->end()
                                        ->scalarNode('tag')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Provider\\Tag\\DefaultProvider')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('context')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('provider')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('context')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('provider')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('context')
                                    ->useAttributeAsKey('id')
                                    ->prototype('array')
                                        ->children()
                                            ->scalarNode('provider')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
