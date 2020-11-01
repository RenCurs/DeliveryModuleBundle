<?php

namespace RetailCrm\DeliveryModuleBundle\IntegrationModule;

use RetailCrm\DeliveryModuleBundle\Model\Account;

interface IntegrationModuleFactoryInterface
{
    public function createIntegrationModule(Account $account): array;
}
