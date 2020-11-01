<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

interface AccountManagerInterface
{
    public function getClass(): string;

    public function createAccount(): object;

    public function findAccountBy(array $criteria): ?object;

    public function findAccountById(int $id): ?object;

    public function findAccountByClientId(string $clientId): ?object;

    public function findActiveAccountById(int $id): ?object;

    public function findActiveAccountByClientId(string $clientId): ?object;

    public function findActiveAccounts(): \Generator;

    public function saveAccount(Account $account): void;
}
