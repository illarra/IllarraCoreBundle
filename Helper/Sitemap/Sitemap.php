<?php

namespace Illarra\CoreBundle\Helper\Sitemap;

class Sitemap
{
    protected $urls = [];
    
    public function addUrl(Url $url)
    {
        $this->urls[] = $url;
    }
    
    public function render()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($this->urls as $url) {
            echo '<url>';
            echo '<loc>'.$url->getLoc().'</loc>';
            echo '<priority>'.$url->getPriority().'</priority>';
            echo '</url>';
        }
        
        echo '</urlset>';
        
        return ob_get_clean();
    }
    
    public function renderIndex()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($this->urls as $url) {
            echo '<sitemap><loc>'.$url->getLoc().'</loc></sitemap>';
        }
        
        echo '</sitemapindex>';
        
        return ob_get_clean();
    }
}
