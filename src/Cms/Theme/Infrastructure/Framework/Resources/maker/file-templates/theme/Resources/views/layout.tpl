<!doctype html>
<html lang="{{ page_locale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{% block title %}{{ title() }}{% endblock %}</title>
        {{ do_action('theme.head') }}
        {% block head %}{% endblock %}
    </head>
    <body class="{{ body_class(app.request) }}">
        {% block beforebody %}{% endblock %}

        {% block content %}{% endblock %}

        {{ do_action('theme.body') }}
        {% block afterbody %}{% endblock %}
    </body>
</html>
