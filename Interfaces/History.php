<?php

namespace Atournayre\Bundle\HistoriqueBundle\Interfaces;

use DateTimeInterface;

interface History
{
    public function getId(): int;

    public function getEntityChangeSet(): string;

    public function setEntityChangeSet(string $entityChangeSet): self;

    public function getEntityChangeSetAsArray(): array;

    public function getPreviousValues(): array;

    /**
     * @param string $fieldName The name of the property to retrieve in previous value.
     *
     * @return array
     */
    public function getPreviousValueByName(string $fieldName): array;

    public function getAt(): DateTimeInterface;

    public function setAt(DateTimeInterface $at): self;
}
