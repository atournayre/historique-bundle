<?php

namespace Atournayre\Bundle\HistoriqueBundle\Factory;

use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;
use Atournayre\Bundle\HistoriqueBundle\Service\Serializer;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HistoryFactory
{
    /**
     * @param Serializer            $serializer
     * @param string                $historyClassName The fully qualified class name or the History entity.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        private readonly Serializer            $serializer,
        private readonly string                $historyClassName,
        private readonly TokenStorageInterface $tokenStorage
    ) {
    }

    /**
     * @param array $changeSet
     *
     * @return HistoryInterface
     */
    public function create(array $changeSet): HistoryInterface
    {
        $entityChangeSet = $this->serializer->serialize($changeSet);

        $fqdnHistory = $this->historyClassName;

        return (new $fqdnHistory())
            ->setBy($this->tokenStorage->getToken()->getUser())
            ->setAt(new DateTime())
            ->setEntityChangeSet($entityChangeSet);
    }
}
