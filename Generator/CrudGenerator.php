<?php

namespace Illarra\CoreBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\DoctrineCrudGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class CrudGenerator extends DoctrineCrudGenerator
{
    public function generate(BundleInterface $bundle, $entity, ClassMetadataInfo $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite)
    {
        parent::generate($bundle, $entity, $metadata, $format, $routePrefix, $needWriteActions, $forceOverwrite);
        
        $dir = sprintf('%s/Resources/views/%s', $this->bundle->getPath(), str_replace('\\', '/', $this->entity));
        
        if (in_array('new', $this->actions) || in_array('edit', $this->actions)) {
            $this->generateFormView($dir);
        }
    }
    
    /**
     * @param string $dir The path to the folder that hosts templates in the bundle
     */
    protected function generateFormView($dir)
    {
        $this->renderFile($this->skeletonDir, 'views/_form.html.twig.twig', $dir.'/_form.html.twig', array());
    }
}
