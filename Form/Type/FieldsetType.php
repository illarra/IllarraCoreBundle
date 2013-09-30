<?php

namespace Illarra\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FieldsetType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'virtual' => true,
            'label' => false
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'fieldset';
    }
}
