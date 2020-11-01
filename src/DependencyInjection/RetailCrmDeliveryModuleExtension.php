<?php

namespace RetailCrm\DeliveryModuleBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class RetailCrmDeliveryModuleExtension extends Extension
{
    private const SERVICES = [
        'commands',
        'controllers',
        'integrations',
        'serializers',
        'validators',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));

        if ('custom' !== $config['db_driver']) {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        }

        foreach (self::SERVICES as $service) {
            $loader->load(sprintf('%s.xml', $service));
        }

        // Models
        $container->setParameter('retail_crm_delivery_module.model.account.class', $config['model']['account_class']);
        $container->setParameter('retail_crm_delivery_module.model.delivery.class', $config['model']['delivery_class']);

        // Services
        $container->setAlias('retail_crm_delivery_module.manager.account', $config['service']['manager']['account']);
        $container->getAlias('retail_crm_delivery_module.manager.account')->setPublic(true);

        $container->setAlias('retail_crm_delivery_module.manager.delivery', $config['service']['manager']['delivery']);
        $container->getAlias('retail_crm_delivery_module.manager.delivery')->setPublic(true);

        if ($config['use_tracking']) {
            $loader->load('tracker.xml');

            $container->setAlias('retail_crm_delivery_module.tracker', $config['service']['tracker']);
            $container->getAlias('retail_crm_delivery_module.tracker')->setPublic(true);
        } else {
            $container->removeDefinition('retail_crm_delivery_module.command.tacking');
        }

        // Integration module
        $container->setAlias('retail_crm_delivery_module.integration_module.factory', $config['integration_module']['factory']);
        $container->getAlias('retail_crm_delivery_module.integration_module.factory')->setPublic(true);

        $container->setParameter('retail_crm_delivery_module.integration_module.configuration', $config['integration_module']['configuration']);

        // Delivery service
        if (isset($config['delivery_service']['adapter']['tariff'])) {
            $container->setAlias('retail_crm_delivery_module.delivery_service.adapter.tariff', $config['delivery_service']['adapter']['tariff']);
            $container->getAlias('retail_crm_delivery_module.delivery_service.adapter.tariff')->setPublic(true);
        }

        if (isset($config['delivery_service']['adapter']['delivery'])) {
            $container->setAlias('retail_crm_delivery_module.delivery_service.adapter.delivery', $config['delivery_service']['adapter']['delivery']);
            $container->getAlias('retail_crm_delivery_module.delivery_service.adapter.delivery')->setPublic(true);
        }

        if (isset($config['delivery_service']['adapter']['shipment'])) {
            $container->setAlias('retail_crm_delivery_module.delivery_service.adapter.shipment', $config['delivery_service']['adapter']['shipment']);
            $container->getAlias('retail_crm_delivery_module.delivery_service.adapter.shipment')->setPublic(true);
        }

        if (isset($config['delivery_service']['adapter']['plate'])) {
            $container->setAlias('retail_crm_delivery_module.delivery_service.adapter.plate', $config['delivery_service']['adapter']['plate']);
            $container->getAlias('retail_crm_delivery_module.delivery_service.adapter.plate')->setPublic(true);
        }

        if (isset($config['delivery_service']['adapter']['status'])) {
            $container->setAlias('retail_crm_delivery_module.delivery_service.adapter.status', $config['delivery_service']['adapter']['status']);
            $container->getAlias('retail_crm_delivery_module.delivery_service.adapter.status')->setPublic(true);
        }

        $container->setParameter('retail_crm_delivery_module.delivery_service.parameters', $config['delivery_service']['parameters']);
    }
}
