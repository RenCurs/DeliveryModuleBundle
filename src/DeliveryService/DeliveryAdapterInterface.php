<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService;

use RetailCrm\DeliveryModuleBundle\Model\Account;

interface DeliveryAdapterInterface
{
    public function createDelivery(Account $account, array $data): array;

    public function getDelivery(Account $account, string $externalId): array;

    public function deleteDelivery(Account $account, array $data): bool;
}
