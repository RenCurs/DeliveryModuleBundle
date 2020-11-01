<?php

namespace RetailCrm\DeliveryModuleBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use RetailCrm\DeliveryModuleBundle\Model\Account as BaseAccount;
use RetailCrm\DeliveryModuleBundle\Model\AccountManager as BaseAccountManager;

class AccountManager extends BaseAccountManager
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

    public function findAccountBy(array $criteria): ?object
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findAccountById(int $id): ?object
    {
        return $this->findAccountBy(['id' => $id]);
    }

    public function findAccountByClientId(string $clientId): ?object
    {
        return $this->findAccountBy(['clientId' => $clientId]);
    }

    public function findActiveAccountById(int $id): ?object
    {
        return $this->findAccountBy(['id' => $id, 'active' => true, 'freeze' => false]);
    }

    public function findActiveAccountByClientId(string $clientId): ?object
    {
        return $this->findAccountBy(['clientId' => $clientId, 'active' => true, 'freeze' => false]);
    }

    public function findActiveAccounts(): \Generator
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $query = $queryBuilder
            ->select('account')
            ->from(Account::class, 'account')
            ->andWhere('account.active = true')
            ->andWhere('account.freeze = false')
            ->andWhere('account.id > :lastId')
            ->addOrderBy('account.id')
            ->setMaxResults(self::QUERY_MAX_RESULTS)
            ->getQuery()
        ;

        $lastId = 0;
        while (true) {
            $query->setParameter('lastId', $lastId);

            $result = $query->getResult();
            if (empty($result)) {
                yield from [];

                break;
            }

            /** @var Account $item */
            foreach ($result as $item) {
                $lastId = $item->getId();

                yield $item;
            }

            $this->entityManager->clear();
        }
    }

    public function saveAccount(BaseAccount $account): void
    {
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->entityManager->getRepository($this->getClass());
    }
}
