<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

interface DeliveryManagerInterface
{
    public function getClass(): string;

    public function createDelivery(Account $account): Delivery;

    public function findDeliveryBy(array $criteria): ?object;

    public function findDeliveryById(int $id): ?object;

    public function findDeliveryByExternalId(string $externalId): ?object;

    public function findActiveDeliveriesByAccount(Account $account): \Generator;

    public function saveDelivery(Delivery $delivery): void;

    public function removeDelivery(Delivery $delivery): void;
}
