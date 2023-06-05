{% assets ['bootstrap', 'frontend'] %}

<!doctype html>
<html lang="{{ current_website().locale.language }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        {{ do_action('theme.head') }}
        {% block head %}{% endblock %}
    </head>
    <body class="{{ body_class(app.request) }}">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-xxl px-4">
                <a class="navbar-brand" href="{{ path('frontend.homepage') }}">{{ current_website().name }}</a>
                {{ menu_space('mainmenu') }}
            </div>
        </nav>
        {{ breadcrumbs() }}
        <div class="container-xxl">
            <div class="row">
                <div class="col">
                    {{ flashes() }}
                    {% block content %}This is a default theme view. That means there is no Theme defined in CMS.{% endblock %}
                </div>
            </div>
        </div>
        <footer class="border-top">
            <p class="text-center text-muted my-4">{{ 'now'|date('Y') }} &copy; {{ current_website().name }}</p>
        </footer>
        {{ do_action('theme.body') }}
    </body>
</html>
