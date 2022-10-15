<?php

namespace Atournayre\Bundle\HistoriqueBundle\Traits;

use Atournayre\Bundle\HistoriqueBundle\Entity\History;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

trait HistorycableTrait
{
    #[ORM\ManyToMany(targetEntity: HistoryInterface::class, cascade: ['persist'])]
    #[ORM\JoinTable(
        joinColumns: [new ORM\JoinColumn(name: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "id")],
    )]
    private array|Collection $histories;

    /**
     * @return History[]|Collection
     */
    public function getHistories(): Collection|array
    {
        return $this->histories;
    }

    /**
     * @param Collection|History[] $histories
     *
     * @return $this
     */
    public function setHistories(Collection|array $histories): self
    {
        $this->histories = $histories;
        return $this;
    }

    /**
     * @param HistoryInterface $history
     *
     * @return $this
     */
    public function addHistory(HistoryInterface $history): self
    {
        $this->histories[] = $history;

        return $this;
    }

    /**
     * @param string $sort
     *
     * @return array
     */
    public function getAllPreviousValues(string $sort = Criteria::DESC): array
    {
        $previousValues = [];
        foreach ($this->histories->toArray() as $history) {
            $previousValues += $history->getPreviousValues();
        }
        return $this->sortPreviousValues($previousValues, $sort);
    }

    /**
     * @param string $fieldName
     * @param string $sort
     *
     * @return array
     */
    public function getAllPreviousValuesByName(string $fieldName, string $sort = Criteria::DESC): array
    {
        $previousValues = [];
        foreach ($this->histories->toArray() as $history) {
            $previousValues += $history->getPreviousValueByName($fieldName);
        }
        return $this->sortPreviousValues($previousValues, $sort);
    }

    /**
     * @return HistoryInterface
     */
    public function getLastValues(): HistoryInterface
    {
        return $this->histories
            ->last()
            ->getPreviousValues();
    }

    /**
     * @param string $fieldName
     *
     * @return mixed
     */
    public function getLastValuesByName(string $fieldName): mixed
    {
        return $this->histories
            ->last()
            ->getPreviousValueByName($fieldName);
    }

    /**
     * @param array  $previousValues
     * @param string $sort
     *
     * @return array
     */
    private function sortPreviousValues(array $previousValues, string $sort = Criteria::DESC): array
    {
        $sort === Criteria::DESC
            ? krsort($previousValues)
            : ksort($previousValues);
        return $previousValues;
    }
}
