{% assets ['js_cookie'] %}

<script nonce="{{ csp_nonce() }}">
    $('body').on('click', '.tulia-edit-links-toggle', function (e) {
        e.preventDefault();
        let show = Cookies.get('tulia_editlinks_show');

        if (show === 'yes') {
            Cookies.remove('tulia_editlinks_show');
        } else {
            Cookies.set('tulia_editlinks_show', 'yes', { expires: 365 });
        }

        Cookies.remove('tulia-toolbar-opened');
        document.location.reload();
    });
</script>
