<?php

namespace Atournayre\Bundle\HistoriqueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('atournayre');

        $rootNode
            ->children()
                ->scalarNode('history_class')->end();

        return $treeBuilder;
    }
}
