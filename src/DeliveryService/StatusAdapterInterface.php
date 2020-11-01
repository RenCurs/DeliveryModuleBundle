<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService;

use RetailCrm\DeliveryModuleBundle\Model\Delivery;

interface StatusAdapterInterface
{
    public function getStatusList(): array;

    public function getStatus(Delivery $delivery): array;
}
