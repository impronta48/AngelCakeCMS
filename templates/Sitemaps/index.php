<?xml version="1.0" encoding="UTF-8" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd
    http://www.w3.org/1999/xhtml">

  <?php
    use Cake\Routing\Router;
  ?>

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