<?php

namespace Illarra\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class PointToStringTransformer implements DataTransformerInterface
{
    /**
     * @param array $point
     * @return string
     */
    public function transform($point)
    {
        return $point[0].','.$point[1];
    }
    
    /**
     * @param string $string
     * @return array
     */
    public function reverseTransform($string)
    {
        return explode(',', $string);
    }
}
