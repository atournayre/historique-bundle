<?php

namespace Atournayre\Bundle\HistoriqueBundle\Factory;

use Atournayre\Bundle\HistoriqueBundle\Exception\EmptyChangeSetException;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History;
use Atournayre\Bundle\HistoriqueBundle\Config\LoaderConfig;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractFactory
{
    public function __construct(
        protected Collection $changeSet = new ArrayCollection(),
    )
    {
    }

    /**
     * @throws EmptyChangeSetException
     */
    protected function createHistory(?UserInterface $user = null): History
    {
        $loaderConfig = new LoaderConfig();
        $historyClassName = $loaderConfig->getHistoryClassName();

        return (new $historyClassName())
            ->setBy($user)
            ->setAt(new DateTime())
            ->setEntityChangeSet($this->convertChangeSetToString());
    }

    /**
     * @throws EmptyChangeSetException
     */
    private function convertChangeSetToString(): ?string
    {
        if (empty($this->changeSet)) return null;

        if (is_string($this->changeSet)) return $this->changeSet;

        if ($this->changeSet instanceof Collection) {
            if ($this->changeSet->isEmpty()) {
                throw new EmptyChangeSetException();
            }

            return json_encode($this->changeSet->toArray());
        }

        return json_encode($this->changeSet);
    }

    abstract public function create(array $changeSet);
}
