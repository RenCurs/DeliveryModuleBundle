<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService\Exception;

class BadRequestException extends RuntimeException implements ExceptionInterface
{
    public function __construct($message = 'Bad request', \Throwable $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }
}
