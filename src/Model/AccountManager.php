<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

abstract class AccountManager implements AccountManagerInterface
{
    public function createAccount(): object
    {
        $class = $this->getClass();

        return new $class();
    }
}
