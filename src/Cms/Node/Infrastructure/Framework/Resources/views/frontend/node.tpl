{% extends 'theme' %}

{% block content %}
    <h1>{{ node.title }}</h1>

    <p>
        {{ 'publishedAtDate'|trans({ date: format_date(node.publishedAt) }) }}<br />
        {% if category %}
            {{ 'category'|trans }}: <a href="{{ term_path(category) }}">{{ category.name }}</a>
        {% endif %}
    </p>

    {{ edit_links(node) }}

    <p>{{ node.introduction }}</p>

    {% if node.thumbnail %}
        <p>{{ image(node.thumbnail, { size: 'node-thumbnail' }) }}</p>
    {% endif %}

    {{ node.content|raw }}
{% endblock %}
