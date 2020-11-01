<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService;

use RetailCrm\DeliveryModuleBundle\Model\Account;

interface PlateAdapterInterface
{
    public function getPrintedPlates(Account $account, array $data): array;
}
