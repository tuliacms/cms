{% extends 'theme' %}

{% set blocksPriority = ['title', 'details', 'edit_links', 'introduction', 'thumbnail', 'node_content'] %}

{% block title %}<h1>{{ node.title }}</h1>{% endblock %}

{% block details %}
    <p>
        {{ 'publishedAtDate'|trans({ date: format_date(node.publishedAt) }) }}<br />
        {% if category %}
            {{ 'category'|trans }}: <a href="{{ term_path(category) }}">{{ category.name }}</a>
        {% endif %}
    </p>
{% endblock %}

{% block edit_links %}
    {{ edit_links(node) }}
{% endblock %}

{% block introduction %}<p>{{ node.introduction }}</p>{% endblock %}

{% block thumbnail %}
    {% if node.thumbnail %}
        <p>{{ image(node.thumbnail, { size: 'node-thumbnail' }) }}</p>
    {% endif %}
{% endblock %}

{% block node_content %}{{ node.content|raw }}{% endblock %}

{% block content %}
    {% for block in blocksPriority %}
        {{ block(block) }}
    {% endfor %}
{% endblock %}
