<?php

namespace Atournayre\Bundle\HistoriqueBundle\Factory;

use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;
use Atournayre\Bundle\HistoriqueBundle\Service\Serializer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HistoryFactory
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $historyClassName;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param Serializer            $serializer
     * @param string                $historyClassName The fully qualified class name or the History entity.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(Serializer $serializer, $historyClassName, TokenStorageInterface $tokenStorage)
    {
        $this->serializer = $serializer;
        $this->historyClassName = $historyClassName;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param array $changeSet
     *
     * @return HistoryInterface
     */
    public function creer(array $changeSet)
    {
        $entityChangeSet = $this->serializer->serialize($changeSet);

        $fqdnHistory = $this->historyClassName;

        return (new $fqdnHistory() )
            ->setBy($this->tokenStorage->getToken()->getUser())
            ->setAt(new \DateTime())
            ->setEntityChangeSet($entityChangeSet);
    }
}
