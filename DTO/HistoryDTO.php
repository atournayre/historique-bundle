<?php

namespace Atournayre\Bundle\HistoriqueBundle\DTO;

class HistoryDTO
{
    public function __construct(
        public readonly string $title,
        public readonly mixed $oldValue,
        public readonly mixed $newValue,
    )
    {
    }
}
