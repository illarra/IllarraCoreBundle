<?php

namespace Illarra\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Illarra\CoreBundle\Form\DataTransformer\PointToStringTransformer;

class PointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PointToStringTransformer();
        $builder->addModelTransformer($transformer);
    }
    
    public function getParent()
    {
        return 'hidden';
    }
    
    public function getName()
    {
        return 'point';
    }
}
