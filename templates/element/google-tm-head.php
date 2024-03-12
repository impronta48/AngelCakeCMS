<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('consent', 'default', {
        'ad_user_data': 'denied',
        'ad_personalization': 'denied',
        'ad_storage': 'denied',
        'analytics_storage': 'denied',
        'wait_for_update': 500,
    });
    dataLayer.push({
        'gtm.start': new Date().getTime(),
        'event': 'gtm.js'
    });
</script>


<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/cookie-bar/cookiebar-latest.min.js?customize=1&tracking=1&thirdparty=1&always=1&privacyPage=<?= urlencode($privacy_url) ?>"></script>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $id ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', <?= $id ?>);
</script>

<!-- End Google Tag Manager -->

<!-- Consent Manager, as esplained here https://developers.google.com/tag-platform/tag-manager/templates/consent-apis -->
<script>
    // Array of callbacks to be executed when consent changes
    const consentListeners = [];

    /**
     *   Called from GTM template to set callback to be executed when user consent is provided.
     *   @param {function} Callback to execute on user consent
     */
    window.addConsentListenerExample = (callback) => {
        consentListeners.push(callback);
    };

    /**
     *   Called when user grants/denies consent.
     *   @param {Object} Object containing user consent settings.
     */
    const cookiebarConsent = (consent) => {
        consentListeners.forEach((callback) => {
            callback(consent);
        });
    };
</script>