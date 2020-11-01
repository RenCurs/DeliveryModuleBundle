<?php

namespace RetailCrm\DeliveryModuleBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use RetailCrm\DeliveryModuleBundle\Model\Account as BaseAccount;
use RetailCrm\DeliveryModuleBundle\Model\Delivery as BaseDelivery;
use RetailCrm\DeliveryModuleBundle\Model\DeliveryManager as BaseDeliveryManager;

class DeliveryManager extends BaseDeliveryManager
{
    protected const QUERY_MAX_RESULTS = 100;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var string */
    protected $class;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $class
    ) {
        $this->entityManager = $entityManager;
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function findDeliveryBy(array $criteria): ?object
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findDeliveryById(int $id): ?object
    {
        return $this->findDeliveryBy(['id' => $id]);
    }

    public function findDeliveryByExternalId(string $externalId): ?object
    {
        return $this->findDeliveryBy(['externalId' => $externalId]);
    }

    public function findActiveDeliveriesByAccount(BaseAccount $account): \Generator
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('delivery')
            ->from(Delivery::class, 'delivery')
            ->andWhere('delivery.ended = FALSE')
            ->andWhere('delivery.account = :account')
            ->andWhere('delivery.id > :lastId')
            ->orderBy('delivery.id ASC')
            ->setMaxResults(self::QUERY_MAX_RESULTS)
            ->getQuery()
        ;

        $query->setParameter('account', $account);

        $lastId = 0;
        while (true) {
            $query->setParameter('lastId', $lastId);

            $result = $query->getResult();
            if (empty($result)) {
                yield from [];

                break;
            }

            /** @var Delivery $item */
            foreach ($result as $item) {
                $lastId = $item->getId();

                yield $item;
            }

            $this->entityManager->clear();
        }
    }

    public function saveDelivery(BaseDelivery $delivery): void
    {
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();
    }

    public function removeDelivery(BaseDelivery $delivery): void
    {
        $this->entityManager->remove($delivery);
        $this->entityManager->flush();
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository($this->getClass());
    }
}
