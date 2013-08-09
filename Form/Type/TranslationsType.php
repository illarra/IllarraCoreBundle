<?php

namespace Illarra\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationsType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'allow_add'          => true,
            'by_reference'       => false,
        ));
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'translations';
    }
}