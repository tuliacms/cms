<!doctype html>
<html lang="{{ page_locale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        {{ do_action('theme.head') }}
    </head>
    <body class="{{ body_class(app.request) }}">
        {% block content %}{% endblock %}
        {{ do_action('theme.body') }}
    </body>
</html>
