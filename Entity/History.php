<?php

namespace Atournayre\Bundle\HistoriqueBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;

#[ORM\Entity]
#[ORM\Table(name: 'history')]
#[ORM\InheritanceType('SINGLE_TABLE')]
class History implements HistoryInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(type: 'text', nullable: false)]
    protected ?string $entityChangeSet;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected DateTimeInterface $at;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?UserInterface $by;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEntityChangeSet(): string
    {
        return $this->entityChangeSet;
    }

    /**
     * @return array
     */
    public function getEntityChangeSetAsArray(): array
    {
        return json_decode($this->entityChangeSet, JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @return array
     */
    public function getPreviousValues(): array
    {
        $entityChangeSetAsArray = $this->getEntityChangeSetAsArray();
        $previousValues = [];
        $timestamp = $this->at->getTimestamp();
        foreach ($entityChangeSetAsArray as $item => $value) {
            $previousValues[$timestamp][$item] = $value[0];
        }
        return $this->decoratePreviousValues($previousValues, $timestamp);
    }

    public function getPreviousValueByName(string $fieldName): array
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
     * @param string|null $entityChangeSet
     *
     * @return $this
     */
    public function setEntityChangeSet(?string $entityChangeSet): self
    {
        $this->entityChangeSet = $entityChangeSet;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getAt(): DateTimeInterface
    {
        return $this->at;
    }

    /**
     * @param DateTimeInterface $at
     *
     * @return $this
     */
    public function setAt(DateTimeInterface $at): static
    {
        $this->at = $at;
        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getBy(): ?UserInterface
    {
        return $this->by;
    }

    /**
     * @param UserInterface|null $by
     *
     * @return $this
     */
    public function setBy(?UserInterface $by): static
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
    private function decoratePreviousValues(array $previousValues, int $timestamp = null): array
    {
        if (array_key_exists($timestamp, $previousValues)) {
            $previousValues[$timestamp]['_updatedAt'] = $this->at;
            $previousValues[$timestamp]['_updatedBy'] = $this->by;
        }
        return $previousValues;
    }
}
