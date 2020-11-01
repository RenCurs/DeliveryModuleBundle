<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService;

use RetailCrm\DeliveryModuleBundle\Model\Account;

interface TariffAdapterInterface
{
    public function getTariffList(Account $account): array;

    public function calculate(Account $account, array $data): array;
}
