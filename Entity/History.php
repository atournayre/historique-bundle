<?php

namespace Atournayre\Bundle\HistoriqueBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="history")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class History implements HistoryInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $entityChangeSet;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $at;

    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $by;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEntityChangeSet()
    {
        return $this->entityChangeSet;
    }

    /**
     * @return array
     */
    public function getEntityChangeSetAsArray()
    {
        return json_decode($this->entityChangeSet, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @return array
     */
    public function getPreviousValues()
    {
        $entityChangeSetAsArray = $this->getEntityChangeSetAsArray();
        $previousValues = [];
        $timestamp = $this->at->getTimestamp();
        foreach ($entityChangeSetAsArray as $item => $value) {
            $previousValues[$timestamp][$item] = $value[0];
        }
        return $this->decoratePreviousValues($previousValues, $timestamp);
    }

    /**
     * @return array
     */
    public function getPreviousValueByName($fieldName)
    {
        $entityChangeSetAsArray = $this->getEntityChangeSetAsArray();
        $previousValues = [];
        $timestamp = $this->at->getTimestamp();
        foreach ($entityChangeSetAsArray as $item => $value) {
            if ($item === $fieldName) {
                $previousValues[$timestamp][$item] = $value[0];
            }
        }
        return $this->decoratePreviousValues($previousValues, $timestamp);
    }

    /**
     * @param string $entityChangeSet
     *
     * @return $this
     */
    public function setEntityChangeSet($entityChangeSet)
    {
        $this->entityChangeSet = $entityChangeSet;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getAt()
    {
        return $this->at;
    }

    /**
     * @param DateTimeInterface $at
     *
     * @return $this
     */
    public function setAt(DateTimeInterface $at)
    {
        $this->at = $at;
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getBy()
    {
        return $this->by;
    }

    /**
     * @param UserInterface $by
     *
     * @return $this
     */
    public function setBy(UserInterface $by)
    {
        $this->by = $by;
        return $this;
    }

    /**
     * @param array    $previousValues
     * @param int|null $timestamp
     *
     * @return array
     */
    private function decoratePreviousValues(array $previousValues, $timestamp = null)
    {
        if (array_key_exists($timestamp, $previousValues)) {
            $previousValues[$timestamp]['_updatedAt'] = $this->at;
            $previousValues[$timestamp]['_updatedBy'] = $this->by;
        }
        return $previousValues;
    }
}
