<?php

namespace Illarra\CoreBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Illarra\CoreBundle\DependencyInjection\IllarraCoreExtension;

class IllarraCoreExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    /**
     * @return ContainerBuilder
     */
    protected function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    public function setUp()
    {
        $this->extension = new IllarraCoreExtension();
    }

    public function testGetConfigWithDefaultValues()
    {
        $config = [];
        $this->extension->load([$config], $container = $this->getContainer());

        
        $this->assertTrue($container->hasParameter('illarra_core.locales'), 'locales parameter');
        $this->assertEquals([
            'ca' => 'Català',
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Castellano',
            'eo' => 'Esperanto',
            'eu' => 'Euskara',
            'fr' => 'Française',
            'gl' => 'Galego',
            'it' => 'Italiano',
            'nl' => 'Nederlands',
            'pt' => 'Português',
        ], $container->getParameter('illarra_core.locales'), 'Default for locales');

        $this->assertTrue($container->hasParameter('illarra_core.default_locale'), 'default_locale parameter');
        $this->assertEquals('eu', $container->getParameter('illarra_core.default_locale'), 'Default for default_locale');

        $this->assertTrue($container->hasParameter('illarra_core.available_locales'), 'available_locales parameter');
        $this->assertEquals([], $container->getParameter('illarra_core.available_locales'), 'Default for available_locales');

        $this->assertTrue($container->hasParameter('illarra_core.active_locales'), 'active_locales parameter');
        $this->assertEquals([], $container->getParameter('illarra_core.active_locales'), 'Default for active_locales');
    }
}
