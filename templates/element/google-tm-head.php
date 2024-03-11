<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/cookie-bar/cookiebar-latest.min.js?customize=1&tracking=1&thirdparty=1&always=1&refreshPage=1&privacyPage=<?= urlencode($privacy_url) ?>"></script>
<?php if (isset($_COOKIE['cookiebar']) && $_COOKIE['cookiebar'] == "CookieAllowed") : ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start':

                    new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],

                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =

                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);

        })(window, document, 'script', 'dataLayer', "<?= $id ?>");
    </script>

    <!-- End Google Tag Manager -->
<?php endif; ?>