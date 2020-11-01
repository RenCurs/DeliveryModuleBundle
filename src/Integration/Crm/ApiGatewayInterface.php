<?php

namespace RetailCrm\DeliveryModuleBundle\Integration\Crm;

use RetailCrm\DeliveryModuleBundle\Integration\ApiGatewayInterface as BaseApiGatewayInterface;
use RetailCrm\DeliveryModuleBundle\Model\Account;

interface ApiGatewayInterface extends BaseApiGatewayInterface
{
    public function getCredentials(Account $account): array;

    public function getPaymentTypes(Account $account): array;

    public function getSites(Account $account): array;

    public function getStatuses(Account $account): array;

    public function getStores(Account $account): array;

    public function updateIntegrationModule(Account $account, array $integrationModule): void;

    public function updateDeliveryStatuses(Account $account, string $integrationCode, array $statuses): void;

    public function request(Account $account, string $method, string $endpoint, array $options = []): array;
}
