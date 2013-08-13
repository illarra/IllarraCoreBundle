<?php

namespace Illarra\CoreBundle\Controller\Admin;

class ListMapper
{
    protected $closure;

    public function __construct($closure)
    {
        $this->closure = $closure;
    }

    public function getValues($entity)
    {
        $closure = $this->closure;
        $values  = $closure($entity);

        if (property_exists($entity, 'id')) {
            $values['id'] = $entity->getId();
        }

        return $values;
    }
}
