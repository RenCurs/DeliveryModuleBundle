<?php

namespace RetailCrm\DeliveryModuleBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class CrmAccess extends Constraint
{
    /** @var array */
    public $requiredApiMethods = [];

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
