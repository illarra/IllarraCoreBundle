<?php

namespace Illarra\CoreBundle\Twig;

use Symfony\Component\HttpKernel\KernelInterface;

class ClassifyExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'classify' => new \Twig_Filter_Method($this, 'getClass'),
        );
    }
    
    public function getClass($entity)
    {
        return get_class($entity);
    }
    
    public function getName()
    {
        return 'classify_extension';
    }
}
