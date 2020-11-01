<?php

namespace RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception;

class HttpException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }
}
