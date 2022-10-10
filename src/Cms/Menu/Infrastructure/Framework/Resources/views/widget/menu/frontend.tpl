{% extends 'widget' %}

{% block content %}
    <div class="tulia-navbar tulia-navbar-layout-{{ layout }}">
        {{ menu|raw }}
    </div>
{% endblock %}
