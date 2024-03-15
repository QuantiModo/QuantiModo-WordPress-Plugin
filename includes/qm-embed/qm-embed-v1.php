<script>
    console.log("Api host url is " + apiHost);
    if(!accessToken && !cookieAuth) {
        console.warn('No access token and cookieAuth is false. Now will try to authenticate and to get it');
        window.location.replace(siteUrl + '?connect=quantimodo');
    }
    embedUrl= apiHost + "/embeddable/?"
    console.log("Embed url is " + embedUrl);

</script>

<iframe class="<?= $params['iFrameParams']['class'] ?>"
        <?php if(empty($params['iFrameParams']['url'])): ?>
            src="<?= getenv('QM_API_HOST') ?: QMWPAuth::QM_API_HOST ?>/embeddable/?<?= http_build_query($params['getParams']) ?>"
        <?php else: ?>
            src="<?= getenv('QM_API_HOST') ?: QMWPAuth::QM_API_HOST ?><?= $params['iFrameParams']['url'] ?>"
        <?php endif; ?>
        width="<?= $params['iFrameParams']['width'] ?>" height="<?= $params['iFrameParams']['height'] ?>">
</iframe>
