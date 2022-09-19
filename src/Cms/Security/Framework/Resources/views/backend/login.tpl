{% assets ['backend', 'backend.login_form', 'js_cookie'] %}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{ do_action('theme.head') }}
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" type="image/x-icon" href="{{ asset('/assets/core/backend/theme/images/favicon.png') }}">
    <title>{% block title %}Tulia CMS - Administration Panel login{% endblock %}</title>
    {% block head %}{% endblock %}

    <script nonce="{{ csp_nonce() }}">
        let bgImages = {{ bgImages|json_encode|raw }};
        let prefetchImage = function () {
            let image = new Image;
            image.onload = function () {
                $('.background-image').css('background-image', 'url(' + this.src + ')').addClass('active');
            };

            let modificator = (new Date).getTime();
            let randomImage = bgImages[Math.floor(Math.random() * bgImages.length)];
            image.src = randomImage.path + '?ts=' + modificator;
        };

        prefetchImage();

        const LoginFormConfiguration = {
            logo: '{{ asset('/assets/core/backend/theme/images/logo-reverse.svg') }}',
            clientLocales: {{ clientLocale|json_encode|raw }},
            lastUsername: '{{ last_username }}',
            password: '{{ password }}',
            flashes: '{{ flashes() }}',
            error: '{{ error ? error.messageKey|trans(error.messageData, 'auth') : '' }}',
            form: {
                action: '{{ path('backend.login') }}',
                csrfToken: '{{ csrf_token('authenticate') }}',
            },
            translations: {
                en_US: {
                    loginToAdminPanel: '{{ 'loginToAdminPanel'|trans({}, 'auth', 'en_US') }}',
                    didYouForgotPassword: '{{ 'didYouForgotPassword'|trans({}, 'auth', 'en_US') }}',
                    insertEmailToRestorePassword: '{{ 'insertEmailToRestorePassword'|trans({}, 'auth', 'en_US') }}',
                    doProcess: '{{ 'doProcess'|trans({}, 'auth', 'en_US') }}',
                    username: '{{ 'username'|trans({}, null, 'en_US') }}',
                    password: '{{ 'password'|trans({}, null, 'en_US') }}',
                    rememberMe: '{{ 'rememberMe'|trans({}, null, 'en_US') }}',
                    signIn: '{{ 'signIn'|trans({}, null, 'en_US') }}',
                },
                pl_PL: {
                    loginToAdminPanel: '{{ 'loginToAdminPanel'|trans({}, 'auth', 'pl_PL') }}',
                    didYouForgotPassword: '{{ 'didYouForgotPassword'|trans({}, 'auth', 'pl_PL') }}',
                    insertEmailToRestorePassword: '{{ 'insertEmailToRestorePassword'|trans({}, 'auth', 'pl_PL') }}',
                    doProcess: '{{ 'doProcess'|trans({}, 'auth', 'pl_PL') }}',
                    username: '{{ 'username'|trans({}, null, 'pl_PL') }}',
                    password: '{{ 'password'|trans({}, null, 'pl_PL') }}',
                    rememberMe: '{{ 'rememberMe'|trans({}, null, 'pl_PL') }}',
                    signIn: '{{ 'signIn'|trans({}, null, 'pl_PL') }}',
                }
            }
        };
    </script>
</head>
<body class="login-page">
{% block beforebody %}{% endblock %}
<div class="body-container">
    <div class="left-side">
        <div class="slogan">{{ 'unleashYourCreativity'|trans({}, 'auth') }}</div>
        <div class="background-image"></div>
        <div class="image-copyrights">Images by <a href="https://unsplash.com/" target="_blank" rel="noopener noreferer">unsplash.com</a></div>
    </div>
    <div id="login-form" class="right-side"></div>
</div>
{{ do_action('theme.body') }}
{% block afterbody %}{% endblock %}
</body>
</html>
