<?php

namespace RetailCrm\DeliveryModuleBundle\Model;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Package
{
    /**
     * Идентификатор упаковки
     *
     * @var string
     *
     * @Serializer\Groups({"request"})
     * @Serializer\SerializedName("packageId")
     * @Serializer\Type("string")
     */
    public $packageId;

    /**
     * Вес г.
     *
     * @var float
     *
     * @Serializer\Groups({"request", "calculate"})
     * @Serializer\SerializedName("weight")
     * @Serializer\Type("float")
     */
    public $weight;

    /**
     * Ширина мм.
     *
     * @var int
     *
     * @Serializer\Groups({"request", "calculate"})
     * @Serializer\SerializedName("width")
     * @Serializer\Type("integer")
     */
    public $width;

    /**
     * Длина мм.
     *
     * @var int
     *
     * @Serializer\Groups({"request", "calculate"})
     * @Serializer\SerializedName("length")
     * @Serializer\Type("integer")
     */
    public $length;

    /**
     * Высота мм.
     *
     * @var int
     *
     * @Serializer\Groups({"request", "calculate"})
     * @Serializer\SerializedName("height")
     * @Serializer\Type("integer")
     */
    public $height;

    /**
     * Содержимое упаковки
     *
     * @var PackageItem[]
     *
     * @Serializer\Groups({"request"})
     * @Serializer\SerializedName("items")
     * @Serializer\Type("array<RetailCrm\DeliveryModuleBundle\Model\PackageItem>")
     */
    public $items;

    public function __construct($weight = null, $width = null, $length = null, $height = null)
    {
        $this->weight = $weight;
        $this->width = $width;
        $this->length = $length;
        $this->height = $height;
    }

    public function getVolume()
    {
        if (null !== $this->length
            && null !== $this->width
            && null !== $this->height
        ) {
            return $this->length * $this->width * $this->height;
        } else {
            return false;
        }
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata
            ->addPropertyConstraint('weight', new Assert\NotBlank());
    }
}
