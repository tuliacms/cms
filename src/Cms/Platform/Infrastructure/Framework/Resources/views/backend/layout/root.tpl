{% block beforeall %}{% endblock %}

{% if prevent_load_backend_assets is not defined %}
    {% assets ['backend'] %}
{% endif %}

<!doctype html>
<html lang="{{ current_website().locale.language }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="robots" content="noindex,nofollow">
        <link rel="icon" type="image/x-icon" href="{{ asset('/assets/core/backend/theme/images/favicon.png') }}">
        {% if prevent_load_backend_assets is not defined %}
            <script nonce="{{ csp_nonce() }}">
                window.Tulia = {};
                Tulia.Globals = {
                    user: {
                        locale: '{{ user().locale }}',
                    },
                    search_anything: {
                        endpoint: '{{ path('backend.search.search') }}',
                    },
                    password_protection: {
                        endpoint: '{{ path('backend.security.verify_password') }}',
                    },
                };
            </script>
        {% endif %}
        {% block beforehead %}{% endblock %}
        {{ do_action('theme.head') }}
        <title>{% block title %}{{ title('Tulia CMS Administration Panel') }}{% endblock %}</title>
        {% block head %}{% endblock %}
    </head>
    <body>
        {% block beforebody %}{% endblock %}

        {% block body %}{% endblock %}

        {{ do_action('theme.body') }}
        <script src="{{ frontend_translations_script() }}"></script>

        {% if prevent_load_backend_assets is not defined %}
            <script nonce="{{ csp_nonce() }}">
                typeof moment !== 'undefined' ? moment.locale('{{ user().locale }}') : null;
                $(() => Tulia.UI.init());
            </script>
        {% endif %}

        {% block afterbody %}{% endblock %}
    </body>
</html>

{% block afterall %}{% endblock %}
