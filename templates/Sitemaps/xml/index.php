<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd
    http://www.w3.org/1999/xhtml">

  <?php
    use Cake\Routing\Router;
  ?>

  <url>
    <loc><?php echo Router::url('/', true); ?></loc>
    <xhtml:link rel="alternate" hreflang="it" href="<?php echo Router::url('/ita', true); ?>" />
    <xhtml:link rel="alternate" hreflang="en" href="<?php echo Router::url('/eng', true); ?>" />

    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/pages/contact', true); ?></loc>
    <xhtml:link rel="alternate" hreflang="it" href="<?php echo Router::url('/ita/pages/contact', true); ?>" />
    <xhtml:link rel="alternate" hreflang="en" href="<?php echo Router::url('/eng/pages/contact', true); ?>" />
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/pages/chi-siamo', true); ?></loc>
    <xhtml:link rel="alternate" hreflang="it" href="<?php echo Router::url('/ita/pages/chi-siamo', true); ?>" />
    <xhtml:link rel="alternate" hreflang="en" href="<?php echo Router::url('/eng/pages/chi-siamo', true); ?>" />
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/pages/faq', true); ?></loc>
    <xhtml:link rel="alternate" hreflang="it" href="<?php echo Router::url('/ita/pages/faq', true); ?>" />
    <xhtml:link rel="alternate" hreflang="en" href="<?php echo Router::url('/eng/pages/faq', true); ?>" />
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc><?php echo Router::url('/pages/aziende', true); ?></loc>
    <xhtml:link rel="alternate" hreflang="it" href="<?php echo Router::url('/ita/pages/aziende', true); ?>" />
    <xhtml:link rel="alternate" hreflang="en" href="<?php echo Router::url('/eng/pages/aziende', true); ?>" />
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc>https://b2b.bikesquare.eu/blog</loc>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc>https://b2b.bikesquare.eu/blog/try-and-buy-ebike-usate</loc>
    <changefreq>weekly</changefreq>
    <priority>0.9</priority>
  </url>
  <?php foreach ($destinations as $dest) : ?>
    <url>
      <loc><?= Router::url("/pages/app?destination={$dest->name}", true) ?></loc>
      <xhtml:link rel="alternate" hreflang="it" href="<?php echo Router::url("/ita/pages/app?destination={$dest->name}", true); ?>" />
      <xhtml:link rel="alternate" hreflang="en" href="<?php echo Router::url("/eng/pages/app?destination={$dest->name}", true); ?>" />
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
    </url>
  <?php endforeach; ?>

  <?php foreach ($static as $s) : ?>
    <url>
      <loc><?= $s['dati']['canonical'] ?></loc>
      <lastmod><?php echo $this->Time->toAtom($s['dati']['date']) ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.5</priority>
    </url>
  <?php endforeach; ?>


  <?php foreach ($static as $s) : ?>
    <url>
      <loc><?= $s['dati']['canonical'] ?></loc>
      <lastmod><?php echo $this->Time->toAtom($s['dati']['date']) ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.5</priority>
    </url>
  <?php endforeach; ?>

  <?php foreach ($articles as $a) : ?>
    <url>
      <loc><?= Router::url($a->slug, true) ?></loc>
      <lastmod><?php echo $this->Time->toAtom($a['modified']) ?></lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.5</priority>
    </url>
  <?php endforeach; ?>
</urlset>