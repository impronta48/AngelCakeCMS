<?php

use Cake\Core\Configure;

if (Configure::read('debug') == true) return; ?>


<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/cookie-bar/cookiebar-latest.min.js?customize=1&tracking=1&thirdparty=1&always=1&privacyPage=<?= urlencode($privacy_url) ?>"></script>



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
