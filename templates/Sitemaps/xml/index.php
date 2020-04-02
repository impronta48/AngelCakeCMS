<?xml version="1.0" encoding="UTF-8" ?>
<?xml-stylesheet type="text/xsl" href="https://gitcdn.xyz/repo/pedroborges/xml-sitemap-stylesheet/master/sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd
    http://www.w3.org/1999/xhtml">
<?php use Cake\Routing\Router; ?>
  <url>
    <loc><?php echo Router::url('/', true); ?></loc>
    <xhtml:link 
               rel="alternate"
               hreflang="it"
               href="<?php echo Router::url('/', true); ?>"/>
    <xhtml:link 
               rel="alternate"
               hreflang="en"
               href="<?php echo Router::url('/en', true); ?>"/>

    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/portfolio', true); ?></loc>
    <xhtml:link 
               rel="alternate"
               hreflang="it"
               href="<?php echo Router::url('/portfolio', true); ?>"/>
    <xhtml:link 
               rel="alternate"
               hreflang="en"
               href="<?php echo Router::url('/en/portfolio', true); ?>"/>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/contact', true); ?></loc>
    <xhtml:link 
               rel="alternate"
               hreflang="it"
               href="<?php echo Router::url('/contact', true); ?>"/>
    <xhtml:link 
               rel="alternate"
               hreflang="en"
               href="<?php echo Router::url('/en/contact', true); ?>"/>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/blog', true); ?></loc>
    <xhtml:link 
               rel="alternate"
               hreflang="it"
               href="<?php echo Router::url('/blog', true); ?>"/>
    <xhtml:link 
               rel="alternate"
               hreflang="en"
               href="<?php echo Router::url('/en/blog', true); ?>"/>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/marketplace', true); ?></loc>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  
  <?php foreach ($static as $s): ?>    
    <url>
        <loc><?= $s['dati']['canonical'] ?></loc>
        <lastmod><?php echo $this->Time->toAtom($s['dati']['date']) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>    
  <?php endforeach; ?>
</urlset>