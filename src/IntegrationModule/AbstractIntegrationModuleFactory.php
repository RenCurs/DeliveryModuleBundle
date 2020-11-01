<?php

namespace RetailCrm\DeliveryModuleBundle\IntegrationModule;

use RetailCrm\DeliveryModuleBundle\Model\Account;

abstract class AbstractIntegrationModuleFactory implements IntegrationModuleFactoryInterface
{
    /** @var array */
    protected $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function createIntegrationModule(Account $account): array
    {
        return [
            'code' => sprintf('%s-%s', $this->configuration['integration_code'], $account->getId()),
            'integrationCode' => $this->configuration['integration_code'],
            'active' => $account->isActive(),
            'freeze' => $account->isFrozen(),
            'name' => $this->configuration['locales'][$account->getLanguage()]['name'],
            'logo' => $this->configuration['locales'][$account->getLanguage()]['logo'],
            'clientId' => $account->getClientId(),
            'baseUrl' => $this->getBaseUrl(),
            'actions' => [
                'activity' => 'integration-module/activity',
            ],
            'availableCountries' => $this->configuration['countries'],
            'accountUrl' => $this->getAccountUrl(),
            'integrations' => [
                'delivery' => $this->getDeliveryConfiguration($account),
            ],
        ];
    }

    abstract protected function getBaseUrl(): string;

    abstract protected function getAccountUrl(): string;

    abstract protected function getDeliveryConfiguration(Account $account): array;
}
