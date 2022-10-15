<?php

namespace Atournayre\Bundle\HistoriqueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class HistoriqueExtension extends Extension
{
    public function getAlias(): string
    {
        return 'atournayre_historique';
    }

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        if (!array_key_exists('history_class', $config)) {
            throw new Exception('The option "history_class" must be provided in config.yml.');
        }

        $definition = $container->findDefinition('atournayre.history_bundle.history_factory');
        $definition->replaceArgument(1, $config['history_class']);
    }
}
