<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sonata\TranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Nicolas Bastien <nbastien.pro@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sonata_translation');

        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('default_locale')->end()
                ->arrayNode('gedmo')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('implements')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('instanceof')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('knplabs')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('implements')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('instanceof')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('phpcr')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('implements')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('instanceof')
                            ->requiresAtLeastOneElement()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
