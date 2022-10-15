<?php

namespace Atournayre\Bundle\HistoriqueBundle\EventSubscriber;

use Atournayre\Bundle\HistoriqueBundle\Factory\HistoryFactory;
use Atournayre\Bundle\HistoriqueBundle\Traits\HistorycableInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

class HistoryEventSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HistoryFactory $historyFactory,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $eventArgs
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

            $history = $this->historyFactory->create($changeSet);
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
