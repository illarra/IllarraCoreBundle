<?php

namespace Illarra\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SeoController extends Controller
{   
    /**
     * @Route("/sitemap.xml", name="illarra_seo_sitemap", defaults={"_format": "xml"})
     * @Template()
     */
    public function sitemapAction()
    {
        return array();
    }
    
    /**
     * @Route("/robots.txt", name="illarra_seo_robots", defaults={"_format": "txt"})
     * @Template()
     */
    public function robotsAction()
    {
        return array();
    }
    
    /**
     * @Route("/humans.txt", name="illarra_seo_humans", defaults={"_format": "txt"})
     * @Template()
     */
    public function humansAction()
    {
        return array();
    }
}
