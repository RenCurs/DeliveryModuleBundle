Getting started with RetailCrmDeliveryModuleBundle
==================================================

## Prerequisites

This version of the bundle requires Symfony 5.1.

#### Translations

If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config:

``` yaml
    # config/packages/translation.yaml

    framework:
        translator: { fallback: en }
```

For more information about translations, check [Symfony documentation](http://symfony.com/doc/current/book/translation.html).


## Installation

Installation is a 7 steps process:

1. Download RetailCrmDeliveryModuleBundle
2. Enable the Bundle
3. Create your model class
4. Create your integration module factory
5. Create your delivery service
6. Create your tracker
7. Configure the RetailCrmDeliveryModuleBundle


### Step 1: Install RetailCrmDeliveryModuleBundle

The preferred way to install this bundle is to rely on [Composer](http://getcomposer.org).

Just check on [Packagist](http://packagist.org/packages/retailcrm/delivery-module-bundle) the version you want to install (in the following example, we used "dev-master") and add it to your `composer.json`:

``` json
{
    "require": {
        // ...
        "retailcrm/delivery-module-bundle": "dev-master"
    }
}
```


### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// config/bundles.php

return [
    // ...
    RetailCrm\DeliveryModuleBundle\RetailCrmDeliveryModuleBundle::class => ['all' => true],
];
```


### Step 3: Create model classes

This bundle needs to persist some classes to a database:

- `Account`
- `Delivery`

Your first job, then, is to create these classes for your application.
These classes can look and act however you want: add any properties or methods you find useful.

These classes have just a few requirements:

1. They must extend one of the base classes from the bundle
2. They must have an `id` field

In the following sections, you'll see examples of how your classes should
look, depending on how you're storing your data.

Your classes can live inside any bundle in your application.

**Warning:**

> If you override the __construct() method in your classes, be sure to call parent::__construct(), as the base class depends on this to initialize some fields.


#### Doctrine ORM classes

If you're persisting your data via the Doctrine ORM, then your classes should live in the `Entity` namespace of your bundle and look like this to start:

``` php
<?php
// src/App/Entity/Account.php

namespace App\Entity;

use RetailCrm\DeliveryModuleBundle\Entity\Account as BaseAccount;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Account extends BaseAccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
```

``` php
<?php
// src/App/Entity/Delivery.php

namespace Acme\ApiBundle\Entity;

use RetailCrm\DeliveryModuleBundle\Entity\Delivery as BaseDelivery;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Delivery extends BaseDelivery
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $account;
}
```

__Note__: If you don't have `auto_mapping` activated in your doctrine configuration you need to add `RetailCrmDeliveryModuleBundle` to your mappings in `config/packages/doctrine.yaml`.

### Custom database driver
The bundle provides driver for Doctrine ORM.

Though sometimes you might want to use the bundle with a custom or in-house written storage.
For that, the bundle has support for custom storage.
Once set, setting `manager` options in `retail_crm_delivery_module.service` section becomes mandatory.


### Step 4: Create your integration module factory
The IntegrationModuleFactory class responsibility is to build integration module configuration for RetailCRM.

A custom integration module factory needs to implement `RetailCrn\DeliveryModuleBundle\Integration\Crm\IntegrationModuleFactoryInterface` or extend `RetailCrn\DeliveryModuleBundle\Integration\Crm\AbstractIntegrationModuleFactory`, which makes creating an integration module factory even easier:

``` php
# src/Integration/Crm/CustomIntegrationModuleFactory.php

use RetailCrm\DeliveryModuleBundle\Integration\Crm\AbstractIntegrationModuleFactory;
use RetailCrm\DeliveryModuleBundle\Model\Account;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CustomIntegrationModuleFactory extends AbstractIntegrationModuleFactory
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, array $configuration)
    {
        $this->urlGenerator = $urlGenerator;

        parent::__construct($configuration);
    }

    protected function getBaseUrl(): string
    {
        return $this->urlGenerator->generate('base_url', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function getAccountUrl(): string
    {
        return $this->urlGenerator->generate('account_url', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function getDeliveryConfiguration(Account $account): array
    {
        $configuration = [];

        $configuration['description'] = sprintf("Account %s[%s] configuration", $account->getUrl(), $account->getClientId());
        
        $configuration['actions'] = [
            'calculate' => 'delivery/calculate',
            'save' => 'delivery/save',
            'get' => 'delivery/get',
            'delete' => 'delivery/delete',
            'shipmentPointList' => 'delivery/shipment-point-list',
            'shipmentSave' => 'delivery/shipment-save',
            'shipmentDelete' => 'delivery/shipment-delete',
            'tariffList' => 'delivery/tariff-list',
            'print' => 'delivery/print',
        ];

        $configuration['payerType'] = [
            'receiver',
            'sender',
        ];

        // TODO: add your logic

        return $configuration;
    }
}
```


### Step 5: Create your delivery service
The DeliveryService class responsibility is to provide a gateway for delivery API.

Custom delivery service must implement:
- `RetailCrm\DeliveryModuleBundle\DeliveryService\ProcessableDeliveryServiceInterface` if delivery provides create/delete processing
- `RetailCrm\DeliveryModuleBundle\DeliveryService\TrackableDeliveryServiceInterface` if delivery provides tracking service


### Step 6: Create your tracker
The Tracker class responsibility is to provide a tracking service.

You can use default tracker `retail_crm_delivery_module.tracker.default` or crete your custom.

Tracker is class must implement the `RetailCrm\DeliveryModuleBundle\Tracking\TrackerInterface`.
This interface defines one method called `tracking` to perform delivery tracking.


### Step 7: Configure RetailCrmDeliveryModuleBundle

Import the routing configuration file in `config/routes.yaml`:

``` yaml
# config/routes.yaml
retail_crm_delivery_module_callback_delivery:
    resource: "@RetailCrmDeliveryModuleBundle/Resources/config/routing/delivery_callback.xml"

retail_crm_delivery_module_callback_integration_module:
    resource: "@RetailCrmDeliveryModuleBundle/Resources/config/routing/integration_module_callback.xml"
```

Add RetailCrmDeliveryModuleBundle settings in `config/packages/retail_crm_delivery_module.yaml`:

``` yaml
# config/packages/retail_crm_delivery_module.yaml
retail_crm_delivery_module:
    db_driver: orm # Drivers available: orm or custom
    
    model:
        account_class:  App\Entity\Account
        delivery_class: App\Entity\Delivery

    service:
        manager:
            account: retail_crm_delivery_module.manager.account.default # Or your custom
            delivery: retail_crm_delivery_module.manager.delivery.default # Or your custom
        integration_module_factory: App\IntegrationModule\CustomIntegrationModuleFactory
        delivery_service: App\DeliveryService\CustomDeliveryService
        tracker: retail_crm_delivery_module.tracker.default # Or your custom
```

Update your database schema

Now the bundle is configured, the last thing you need to do is update your database schema because you have added a new entity.

For ORM run the following command.

    $ php bin/console doctrine:schema:update --force
