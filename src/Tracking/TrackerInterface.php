<?php

namespace RetailCrm\DeliveryModuleBundle\Tracking;

use RetailCrm\DeliveryModuleBundle\Model\Account;

interface TrackerInterface
{
    public function tracking(Account $account): void;
}
