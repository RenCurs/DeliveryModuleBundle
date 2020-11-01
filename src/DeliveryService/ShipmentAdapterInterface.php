<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService;

use RetailCrm\DeliveryModuleBundle\Model\Account;

interface ShipmentAdapterInterface
{
    public function getShipmentPointList(Account $account, array $query): array;

    public function createShipment(Account $account, array $data): array;

    public function deleteShipment(Account $account, array $data): bool;
}
