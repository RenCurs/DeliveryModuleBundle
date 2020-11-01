<?php

namespace RetailCrm\DeliveryModuleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const AVAILABLE_DRIVERS = ['orm', 'custom'];
    private const AVAILABLE_LOCALES = ['en', 'es', 'ru'];

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('retail_crm_delivery_module');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('db_driver')
                    ->cannotBeEmpty()
                    ->cannotBeOverwritten()
                    ->validate()
                        ->ifNotInArray(self::AVAILABLE_DRIVERS)
                        ->thenInvalid('The driver %s is not supported. Please choose one of ' . json_encode(self::AVAILABLE_DRIVERS))
                    ->end()
                ->end()

                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('account_class')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('delivery_class')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()

                ->booleanNode('use_tracking')->defaultValue(true)->end()

                ->arrayNode('service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('manager')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('account')
                                    ->defaultValue('retail_crm_delivery_module.manager.account.default')
                                ->end()
                                ->scalarNode('delivery')
                                    ->defaultValue('retail_crm_delivery_module.manager.delivery.default')
                                ->end()
                            ->end()
                        ->end()

                        ->scalarNode('tracker')
                            ->defaultValue('retail_crm_delivery_module.tracker.default')
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('integration_module')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('factory')->isRequired()->cannotBeEmpty()->end()

                        ->arrayNode('configuration')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('integration_code')->isRequired()->cannotBeEmpty()->end()

                                ->arrayNode('locales')
                                    ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                                            ->scalarNode('logo')->isRequired()->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                    ->requiresAtLeastOneElement()
                                    ->useAttributeAsKey('locale')
                                    ->validate()
                                        ->ifTrue(function ($v) {
                                            return array_diff(array_keys($v), self::AVAILABLE_LOCALES);
                                        })
                                        ->thenInvalid('The locales is not valid')
                                    ->end()
                                ->end()

                                ->arrayNode('countries')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('delivery_service')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('adapter')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('tariff')->cannotBeEmpty()->end()
                                ->scalarNode('delivery')->cannotBeEmpty()->end()
                                ->scalarNode('shipment')->cannotBeEmpty()->end()
                                ->scalarNode('plate')->cannotBeEmpty()->end()
                                ->scalarNode('status')->cannotBeEmpty()->end()
                            ->end()
                        ->end()

                        ->variableNode('parameters')->end()
                    ->end()
                ->end()
            ->end()

            ->validate()
                ->ifTrue(function ($v) {
                    return 'custom' === $v['db_driver'] && 'retail_crm_delivery_module.manager.account.default' === $v['service']['manager']['account'];
                })
                ->thenInvalid('You need to specify your own account manager service when using the "custom" driver')
            ->end()

            ->validate()
                ->ifTrue(function ($v) {
                    return 'custom' === $v['db_driver'] && 'retail_crm_delivery_module.manager.delivery.default' === $v['service']['manager']['delivery'];
                })
                ->thenInvalid('You need to specify your own delivery manager service when using the "custom" driver')
            ->end()
        ;

        return $treeBuilder;
    }
}
