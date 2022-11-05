<?php

namespace Atournayre\Bundle\HistoriqueBundle\Factory;

use Atournayre\Bundle\HistoriqueBundle\Exception\HistoriqueException;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History;
use Atournayre\Bundle\HistoriqueBundle\Config\LoaderConfig;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractFactory
{
    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        protected Collection $changeSet = new ArrayCollection(),
    )
    {
    }

    /**
     * @throws HistoriqueException
     */
    protected function createHistory(): History
    {
        $loaderConfig = new LoaderConfig();
        $historyClassName = $loaderConfig->getHistoryClassName();

        return (new $historyClassName())
            ->setBy($this->tokenStorage->getToken()?->getUser())
            ->setAt(new DateTime())
            ->setEntityChangeSet($this->convertChangeSetToString());
    }

    /**
     * @throws HistoriqueException
     */
    private function convertChangeSetToString(): ?string
    {
        if (empty($this->changeSet)) return null;

        if (is_string($this->changeSet)) return $this->changeSet;

        if ($this->changeSet instanceof Collection) {
            if ($this->changeSet->isEmpty()) {
                throw HistoriqueException::emptyChangeSet();
            }

            return json_encode($this->changeSet->toArray());
        }

        return json_encode($this->changeSet);
    }

    abstract public function create(array $changeSet);
}
