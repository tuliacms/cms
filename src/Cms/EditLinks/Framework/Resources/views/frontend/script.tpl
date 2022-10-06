{% assets ['js_cookie'] %}

<script nonce="{{ csp_nonce() }}">
    $(function () {
        function getCookie (name) {
            let value = `; ${document.cookie}`;
            let parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        $('body').on('click', '.tulia-edit-links-toggle', function (e) {
            e.preventDefault();
            let show = getCookie('tulia-editlinks-show');

            if (show === 'yes') {
                document.cookie = `tulia-editlinks-show=; path=/; max-age=0;`;
            } else {
                document.cookie = `tulia-editlinks-show=yes; path=/; max-age=${60 * 60 * 24 * 365}`;
            }

            document.cookie = `tulia-toolbar-opened=; path=/; max-age=0;`;
            document.location.reload();
        });
    });
</script>
