<?php

namespace Atournayre\Bundle\HistoriqueBundle\DependencyInjection;

use Symfony\Component\Asset\Package;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('atournayre');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('history_class')->end()
                ->arrayNode('mappings')
                    ->prototype('scalar')
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}
