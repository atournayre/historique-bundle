<?php

namespace Atournayre\Bundle\HistoriqueBundle\EventSubscriber;

use Atournayre\Bundle\HistoriqueBundle\Exception\HistoriqueException;
use Atournayre\Bundle\HistoriqueBundle\Traits\HistorycableInterface;
use Atournayre\Bundle\HistoriqueBundle\Factory\FactoryLoader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Exception;

class HistoryEventSubscriber implements EventSubscriber
{
    private FactoryLoader $factoryLoader;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenStorageInterface $tokenStorage,
    )
    {
        $this->factoryLoader = new FactoryLoader($tokenStorage);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @throws HistoriqueException
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getObjectManager();
        $uow = $em->getUnitOfWork();

        /** @var HistorycableInterface $entity */
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$this->supports($entity)) {
                continue;
            }
            $changeSet = $uow->getEntityChangeSet($entity);

            $history = ($this->factoryLoader)($entity, $changeSet);

            if (is_null($history)) continue;

            $this->entityManager->persist($history);

            $entity->addHistory($history);

            $this->entityManager->persist($entity);
        }

        $uow->computeChangeSets();
    }

    /**
     * @param object $entity
     *
     * @return bool
     */
    private function supports(object $entity): bool
    {
        return $entity instanceof HistorycableInterface;
    }
}
