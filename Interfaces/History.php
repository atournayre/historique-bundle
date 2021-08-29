<?php

namespace Atournayre\Bundle\HistoriqueBundle\Interfaces;

use DateTimeInterface;

interface History
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEntityChangeSet();

    /**
     * @param string $entityChangeSet
     *
     * @return string
     */
    public function setEntityChangeSet($entityChangeSet);

    /**
     * @return array
     */
    public function getEntityChangeSetAsArray();

    /**
     * @return array
     */
    public function getPreviousValues();

    /**
     * @param string $fieldName The name of the property to retrieve in previous value.
     *
     * @return array
     */
    public function getPreviousValueByName($fieldName);

    /**
     * @return DateTimeInterface
     */
    public function getAt();

    /**
     * @param DateTimeInterface $at
     *
     * @return self
     */
    public function setAt(DateTimeInterface $at);
}
