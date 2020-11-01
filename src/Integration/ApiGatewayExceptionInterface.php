<?php

namespace RetailCrm\DeliveryModuleBundle\Integration;

interface ApiGatewayExceptionInterface extends \Throwable
{
    public function getErrors(): array;
}
