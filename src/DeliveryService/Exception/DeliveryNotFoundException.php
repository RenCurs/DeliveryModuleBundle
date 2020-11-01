<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService\Exception;

class DeliveryNotFoundException extends RuntimeException
{
    public function __construct($message = 'Delivery not found', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
