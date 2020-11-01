<?php

namespace RetailCrm\DeliveryModuleBundle\DeliveryService\Exception;

class DeliveryServiceUnavailableException extends RuntimeException
{
    /** @var int */
    protected $retryAfter;

    public function __construct(int $retryAfter = null, string $message = 'Delivery service is not available', \Throwable $previous = null, int $code = 0)
    {
        $this->retryAfter = $retryAfter;

        parent::__construct($message, $code, $previous);
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
