<?php

namespace Illarra\CoreBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use Illarra\CoreBundle\Generator\CrudGenerator;

class GenerateCrudCommand extends GenerateDoctrineCrudCommand
{
    protected $generator;
    
    protected function configure()
    {
        parent::configure();
        
        $this->setName('illarra:generate:crud');
    }
    
    protected function getGenerator(Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle = NULL)
    {
        if (null === $this->generator) {
            $this->generator = new CrudGenerator($this->getContainer()->get('filesystem'), __DIR__.'/../Resources/skeleton/crud');
        }
        
        return $this->generator;
    }
}
