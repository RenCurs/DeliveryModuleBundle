<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

use JMS\Serializer\Annotation as Serializer;

class Store extends BaseStore
{
    /**
     * @var StoreWorkTime
     *
     * @Serializer\Groups({"request"})
     * @Serializer\SerializedName("workTime")
     * @Serializer\Type("RetailCrm\DeliveryModuleBundle\Model\StoreWorkTime")
     */
    public $workTime;
}
