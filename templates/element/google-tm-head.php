<?php

use Cake\Core\Configure;

if (Configure::read('debug') == true) return; ?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $id ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', "<?= $id ?>");
</script>
<!-- End Google Tag Manager -->
