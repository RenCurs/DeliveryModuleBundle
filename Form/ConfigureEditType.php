<?php

namespace RetailCrm\DeliveryModuleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigureEditType extends AbstractType
{
    /**
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('connectionId', null, [
                'label' => 'label.connectionId',
                'required' => true,
                'attr' => [
                    'placeholder' => 'label.connectionId',
                ],
            ])
            ->add('crmKey', null, [
                'label' => 'label.crmKey',
                'required' => true,
                'attr' => [
                    'placeholder' => 'label.crmKey',
                ],
            ]);
    }
}
