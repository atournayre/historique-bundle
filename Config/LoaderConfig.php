<?php

namespace Atournayre\Bundle\HistoriqueBundle\Config;

use Symfony\Component\Yaml\Yaml;

class LoaderConfig
{
    private array $config;

    public function __construct()
    {
        $configFile = \dirname(__DIR__) . '/../../../config/packages/atournayre_historique.yaml';
        if (!file_exists($configFile)) {
            throw new \LogicException(sprintf('The file "%s" is missing.', $configFile));
        }

        $config = Yaml::parseFile($configFile);
        $this->config = $config['atournayre_historique'] ?? [];
    }

    public function getHistoryClassName(): ?string
    {
        return $this->config['history_class'];
    }

    public function getMappings(): array
    {
        return $this->config['mappings'] ?? [];
    }
}
