<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

use JMS\Serializer\Annotation as Serializer;

class BaseStore
{
    /**
     * @var string
     *
     * @Serializer\Groups({"request"})
     * @Serializer\SerializedName("code")
     * @Serializer\Type("string")
     */
    public $code;

    /**
     * @var string
     *
     * @Serializer\Groups({"request"})
     * @Serializer\SerializedName("name")
     * @Serializer\Type("string")
     */
    public $name;
}
