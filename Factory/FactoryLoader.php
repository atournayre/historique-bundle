<?php

namespace Atournayre\Bundle\HistoriqueBundle\Factory;

use Atournayre\Bundle\HistoriqueBundle\Entity\History;
use Atournayre\Bundle\HistoriqueBundle\Config\LoaderConfig;
use Atournayre\Bundle\HistoriqueBundle\Exception\HistoriqueException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FactoryLoader
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
    }

    /**
     * @throws HistoriqueException Each factory may throw this exception.
     */
    public function __invoke(object $object, array $changeSet): ?History
    {
        $loaderConfig = new LoaderConfig();
        $mappings = $loaderConfig->getMappings();

        if (empty($mappings)) return null;

        if (!array_key_exists(get_class($object), $mappings)) return null;

        $factoryClass = $mappings[get_class($object)];

        if (!class_exists($factoryClass)) {
            throw new \LogicException(sprintf('%s do not exists.', $factoryClass));
        }

        $factory = new $factoryClass($this->tokenStorage);

        if (!$factory instanceof AbstractFactory) {
            throw new \LogicException(sprintf('%s must extends from %s.', $factoryClass, AbstractFactory::class));
        }

        return $factory->create($changeSet);
    }
}
