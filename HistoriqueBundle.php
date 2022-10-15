<?php

namespace Atournayre\Bundle\HistoriqueBundle;

use Atournayre\Bundle\HistoriqueBundle\DependencyInjection\HistoriqueExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HistoriqueBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new HistoriqueExtension();
        }
        return $this->extension;
    }
}
