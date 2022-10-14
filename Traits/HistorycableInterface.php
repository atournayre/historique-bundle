<?php

namespace Atournayre\Bundle\HistoriqueBundle\Traits;

use Atournayre\Bundle\HistoriqueBundle\Interfaces\History as HistoryInterface;

interface HistorycableInterface
{
    public function addHistory(HistoryInterface $history): self;
}
