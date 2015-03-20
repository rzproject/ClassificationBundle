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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('rz_classification');

        $this->addBundleSettings($node);
        $this->addModelSection($node);
        $this->addManagerClassSection($node);
        $this->addSettingsSection($node);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addSettingsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('providers')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('category')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default_context')->isRequired()->end()
                            ->arrayNode('contexts')
                                ->useAttributeAsKey('id')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('provider')->isRequired()->end()
                                        ->scalarNode('default_template')->isRequired()->end()
                                        ->arrayNode('templates')
                                            ->isRequired()
                                            ->useAttributeAsKey('id')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('name')->defaultValue('default')->end()
                                                    ->scalarNode('path')->defaultValue('RzClassificationBundle:Category:list.html.twig')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('tag')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default_context')->isRequired()->end()
                            ->arrayNode('contexts')
                                ->useAttributeAsKey('id')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('provider')->isRequired()->end()
                                        ->scalarNode('default_template')->isRequired()->end()
                                        ->arrayNode('templates')
                                            ->isRequired()
                                            ->useAttributeAsKey('id')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('name')->defaultValue('default')->end()
                                                    ->scalarNode('path')->defaultValue('RzClassificationBundle:Tag:list.html.twig')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('collection')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default_context')->isRequired()->end()
                            ->arrayNode('contexts')
                                ->useAttributeAsKey('id')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('provider')->isRequired()->end()
                                        ->scalarNode('default_template')->isRequired()->end()
                                        ->arrayNode('templates')
                                            ->isRequired()
                                            ->useAttributeAsKey('id')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('name')->defaultValue('default')->end()
                                                    ->scalarNode('path')->defaultValue('RzClassificationBundle:Tag:list.html.twig')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
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
    private function addBundleSettings(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Admin\\CategoryAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('RzClassificationBundle:CategoryAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzClassificationBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('RzClassificationBundle:CategoryAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzClassificationBundle:CategoryAdmin:edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('tag')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Admin\\TagAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('RzClassificationBundle:TagAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzClassificationBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('RzClassificationBundle:TagAdmin:list.html.twig')->cannotBeEmpty()->end()
                                        ->scalarNode('edit')->defaultValue('RzClassificationBundle:CRUD:edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('collection')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Admin\\CollectionAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('RzClassificationBundle:CollectionAdmin')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzClassificationBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('list')->defaultValue('RzClassificationBundle:CollectionAdmin:list.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('context')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Rz\\ClassificationBundle\\Admin\\ContextAdmin')->end()
                                ->scalarNode('controller')->cannotBeEmpty()->defaultValue('SonataAdminBundle:CRUD')->end()
                                ->scalarNode('translation')->cannotBeEmpty()->defaultValue('RzClassificationBundle')->end()
                                ->arrayNode('templates')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('edit')->defaultValue('RzClassificationBundle:CRUD:edit.html.twig')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
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
    private function addModelSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tag')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Tag')->end()
                        ->scalarNode('category')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Category')->end()
                        ->scalarNode('collection')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Collection')->end()
                        ->scalarNode('context')->defaultValue('Application\\Sonata\\ClassificationBundle\\Entity\\Context')->end()
                        ->scalarNode('media')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('page')->defaultValue('Application\\Sonata\\PageBundle\\Entity\\Page')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

            /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addManagerClassSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('manager_class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tag')->defaultValue('Sonata\\ClassificationBundle\\Entity\\TagManager')->end()
                        ->scalarNode('category')->defaultValue('Rz\\ClassificationBundle\\Entity\\CategoryManager')->end()
                        ->scalarNode('collection')->defaultValue('Rz\\ClassificationBundle\\Entity\\CollectionManager')->end()
                        ->scalarNode('context')->defaultValue('Rz\\ClassificationBundle\\Entity\\ContextManager')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
