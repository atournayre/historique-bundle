<?php

namespace Atournayre\Bundle\HistoriqueBundle\Exception;

use Exception;

class HistoriqueException extends Exception
{
    public static function incorrectChangeSet(array $changeSet): self
    {
        return new static(
            sprintf(
                'ChangeSet format is incorrect. ChangeSet must be a Doctrine ChangeSet. $changeSet must provide the following keys 0 and 1; provided keys are : %s.',
                implode(', ', array_keys($changeSet))
            )
        );
    }
}
