<?php

namespace RetailCrm\DeliveryModuleBundle\Integration\Crm\Exception;

use RetailCrm\DeliveryModuleBundle\Integration\ApiGatewayExceptionInterface;

class ApiGatewayException extends \RuntimeException implements ApiGatewayExceptionInterface, ExceptionInterface
{
    /** @var array */
    private $errors;

    public function __construct(string $message = null, array $errors = [], \Throwable $previous = null, int $code = 0)
    {
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
