<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService\Exception;

class ShipmentNotFound extends RuntimeException
{
    public function __construct($message = 'Shipment not found', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
