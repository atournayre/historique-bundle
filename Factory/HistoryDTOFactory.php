<?php

namespace Atournayre\Bundle\HistoriqueBundle\Factory;

use Atournayre\Bundle\HistoriqueBundle\DTO\HistoryDTO;
use Atournayre\Bundle\HistoriqueBundle\Exception\HistoriqueException;

final class HistoryDTOFactory
{
    /**
     * @throws HistoriqueException
     */
    public static function createFromChangeSet(string $title, array $changeSet, /* string|callable */ $closure): HistoryDTO
    {
        if ('01' != implode('', array_keys($changeSet))) {
            throw HistoriqueException::incorrectChangeSet($changeSet);
        }

        if (is_string($closure)) return new HistoryDTO($title, $closure, $closure);

        return new HistoryDTO($title, $closure($changeSet[0]), $closure($changeSet[1]));
    }
}
