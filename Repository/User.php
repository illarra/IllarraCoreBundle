<?php

namespace Illarra\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{
    use \Illarra\CoreBundle\Traits\Repository\Paginator;
    
    public function getOrderFields()
    {
        return ['username ASC'];
    }
}
