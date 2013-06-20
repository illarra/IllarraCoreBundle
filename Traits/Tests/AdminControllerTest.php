<?php

namespace Illarra\CoreBundle\Traits\Tests;

use Symfony\Component\HttpFoundation\Request;

trait AdminControllerTest
{
    protected $route_prefix  = null;
    protected $form_data     = null;
    protected $has_show_view = true;
    
    public function setRoutePrefix($route_prefix)
    {
        $this->route_prefix = $route_prefix;
        
        return $this;
    }
    
    public function getRoutePrefix()
    {
        return $this->route_prefix;
    }
    
    public function setFormData($form_data)
    {
        $this->form_data = $form_data;
        
        return $this;
    }
    
    public function getFormData()
    {
        return $this->form_data;
    }
    
    public function getHasShowView()
    {
        return $this->has_show_view;
    }
    
    public function setHasShowView($has_show_view)
    {
        $this->has_show_view = $has_show_view;
        
        return $this;
    }
    
    public function hasShowView()
    {
        return $this->has_show_view;
    }
    
    public function testCompleteScenario()
    {
        Request::enableHttpMethodParameterOverride();
        
        // Create a new client to browse the application
        $client = static::createClient();
        
        // Create new entity
        $crawler = $client->request('GET', $this->getRoutePrefix());
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->filter('.t-new')->link());
        
        $client->submit($crawler->filter('.t-save')->form($this->getFormData()));
        $crawler = $client->followRedirect();
        
        // Check data in the show view
        //$this->assertTrue($crawler->filter('td:contains("TestPostTitle")')->count() > 0);
        
        if ($this->hasShowView()) {
            $crawler = $client->click($crawler->filter('.t-edit')->link());
        }
            
        // Edit the entity
        $client->submit($crawler->filter('.t-save')->form($this->getFormData()));
        $crawler = $client->followRedirect();
            
        // Check the element contains an attribute with value equals "EditedTestLabelTitle"
        //$this->assertTrue($crawler->filter('[value="EditedTestPostTitle"]')->count() > 0);
            
        // Delete the entity
        $client->submit($crawler->filter('.t-delete')->form());
        $crawler = $client->followRedirect();
        
        // Check the entity has been delete on the list
        //$this->assertNotRegExp('/EditedTestPostTitle/', $client->getResponse()->getContent());
    }
}
