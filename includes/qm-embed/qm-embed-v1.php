<script>
    if (!accessToken) {
        console.warn('No access token. Now will try to authenticate and to get it');
        window.location.href = "?connect=quantimodo";
    }
</script>

<iframe class="<?= $params['iFrameParams']['class'] ?>"
        src="https://embed.quantimo.do?<?= http_build_query($params['getParams']) ?>"
        width="<?= $params['iFrameParams']['width'] ?>" height="<?= $params['iFrameParams']['height'] ?>">
</iframe>
