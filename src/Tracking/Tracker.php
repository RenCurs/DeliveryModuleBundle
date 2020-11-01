<?php

namespace RetailCrm\DeliveryModuleBundle\Tracking;

use RetailCrm\DeliveryModuleBundle\DeliveryService\StatusAdapterInterface;
use RetailCrm\DeliveryModuleBundle\Integration\Crm\ApiGatewayInterface as CrmApiGatewayInterface;
use RetailCrm\DeliveryModuleBundle\IntegrationModule\IntegrationModuleFactoryInterface;
use RetailCrm\DeliveryModuleBundle\Model\Account;
use RetailCrm\DeliveryModuleBundle\Model\AccountManagerInterface;
use RetailCrm\DeliveryModuleBundle\Model\DeliveryManagerInterface;

class Tracker implements TrackerInterface
{
    private const LIMIT = 100;

    /** @var AccountManagerInterface */
    private $accountManager;

    /** @var DeliveryManagerInterface */
    private $deliveryManager;

    /** @var StatusAdapterInterface */
    private $statusAdapter;

    /** @var IntegrationModuleFactoryInterface */
    private $integrationModuleFactory;

    /** @var CrmApiGatewayInterface */
    private $crmApiGateway;

    public function __construct(
        AccountManagerInterface $accountManager,
        DeliveryManagerInterface $deliveryManager,
        StatusAdapterInterface $statusAdapter,
        IntegrationModuleFactoryInterface $integrationModuleFactory,
        CrmApiGatewayInterface $crmApiGateway
    ) {
        $this->accountManager = $accountManager;
        $this->deliveryManager = $deliveryManager;
        $this->statusAdapter = $statusAdapter;
        $this->integrationModuleFactory = $integrationModuleFactory;
        $this->crmApiGateway = $crmApiGateway;
    }

    public function tracking(Account $account): void
    {
        $deliveries = $this->deliveryManager->findActiveDeliveriesByAccount($account);

        $statuses = [];
        foreach ($deliveries as $delivery) {
            $statuses[] = $this->statusAdapter->getStatus($delivery);

            if (0 === count($statuses) % self::LIMIT) {
                $this->updateDeliveryStatuses($account, $statuses);

                $statuses = [];
            }
        }

        if (count($statuses) > 0) {
            $this->updateDeliveryStatuses($account, $statuses);
        }

        $account->setTrackedAt(new \DateTime());

        $this->accountManager->saveAccount($account);
    }

    protected function updateDeliveryStatuses(Account $account, array $statuses): void
    {
        $integrationModule = $this->integrationModuleFactory->createIntegrationModule($account);

        $integrationCode = $integrationModule['integrationCode'] ?? null;
        if (null === $integrationCode) {
            throw new \LogicException('Parameter "integrationCode" is required');
        }

        $this->crmApiGateway->updateDeliveryStatuses($account, $integrationCode, $statuses);
    }
}
