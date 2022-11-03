<?php

namespace Atournayre\Bundle\HistoriqueBundle\EventSubscriber;

use Atournayre\Bundle\HistoriqueBundle\Exception\EmptyChangeSetException;
use Atournayre\Bundle\HistoriqueBundle\Traits\HistorycableInterface;
use Atournayre\Bundle\HistoriqueBundle\Factory\FactoryLoader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

            try {
                $history = ($this->factoryLoader)($entity, $changeSet);
                $this->entityManager->persist($history);

                $entity->addHistory($history);
            } catch (EmptyChangeSetException $exception) {
                continue;
            }
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
