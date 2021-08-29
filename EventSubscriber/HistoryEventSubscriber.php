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
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var HistoryFactory
     */
    private $historyFactory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param HistoryFactory         $historyFactory
     */
    public function __construct(EntityManagerInterface $entityManager, HistoryFactory $historyFactory)
    {
        $this->entityManager = $entityManager;
        $this->historyFactory = $historyFactory;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
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
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        /** @var HistorycableInterface $entity */
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$this->supports($entity)) {
                continue;
            }
            $changeSet = $uow->getEntityChangeSet($entity);

            $history = $this->historyFactory->creer($changeSet);
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
    private function supports($entity)
    {
        return $entity instanceof HistorycableInterface;
    }
}
