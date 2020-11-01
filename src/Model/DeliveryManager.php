<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

abstract class DeliveryManager implements DeliveryManagerInterface
{
    public function createDelivery(Account $account): Delivery
    {
        $class = $this->getClass();

        return new $class($account);
    }
}
