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
        {% if prevent_load_backend_assets is not defined %}
            <script nonce="{{ csp_nonce() }}">
                window.Tulia = {};
                Tulia.Globals = {
                    search_anything: {
                        endpoint: '{{ path('backend.search.search') }}'
                    }
                };
            </script>
        {% endif %}
        {% block beforehead %}{% endblock %}
        {{ theme_head() }}
        <title>{% block title %}{{ title('Tulia CMS Backend') }}{% endblock %}</title>
        {% block head %}{% endblock %}
    </head>
    <body>
        {% block beforebody %}{% endblock %}

        {% block body %}{% endblock %}

        {{ theme_body() }}

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
