<?php

namespace Atournayre\Bundle\HistoriqueBundle\Traits;

use Atournayre\Bundle\HistoriqueBundle\Entity\History;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

trait HistorycableTrait
{
    /**
     * @var History[]|Collection
     * @ORM\ManyToMany(targetEntity="Atournayre\Bundle\HistoriqueBundle\Interfaces\History", cascade={"persist"})
     * @ORM\JoinTable(
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="id")},
     * )
     */
    private $histories;

    /**
     * @return History[]|Collection
     */
    public function getHistories()
    {
        return $this->histories;
    }

    /**
     * @param History[]|Collection $histories
     *
     * @return $this
     */
    public function setHistories($histories)
    {
        $this->histories = $histories;
        return $this;
    }

    /**
     * @param HistoryInterface $history
     *
     * @return $this
     */
    public function addHistory(HistoryInterface $history)
    {
        $this->histories[] = $history;

        return $this;
    }

    /**
     * @param string $sort
     *
     * @return array
     */
    public function getAllPreviousValues($sort = Criteria::DESC)
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
    public function getAllPreviousValuesByName($fieldName, $sort = Criteria::DESC)
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
    public function getLastValues()
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
    public function getLastValuesByName($fieldName)
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
    private function sortPreviousValues(array $previousValues, $sort = Criteria::DESC)
    {
        $sort === Criteria::DESC
            ? krsort($previousValues)
            : ksort($previousValues);
        return $previousValues;
    }
}
