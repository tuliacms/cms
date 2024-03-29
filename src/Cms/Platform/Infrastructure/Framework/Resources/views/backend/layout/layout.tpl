{% extends '@backend/layout/root.tpl' %}

{% assets ['backend'] %}

{% block content %}{% endblock %}

{% block breadcrumbsHome %}
    <li class="breadcrumb-item"><a href="{{ path('backend.homepage') }}">{{ 'breadcrumbsHome'|trans }}</a></li>
{% endblock %}
{% block breadcrumbs %}{% endblock %}

{% block body %}
<div id="body-container">
    {% set layout__breadcrumbsHome = block('breadcrumbsHome') %}
    {% set layout__breadcrumbs = block('breadcrumbs') %}
    {% include relative(_self, 'parts/header.tpl') %}
    <main>
        {% include relative(_self, 'parts/sidebar.tpl') %}
        <div class="mobile-menu-content-overlay"></div>
        {{ block('content') }}
    </main>
    {% include relative(_self, 'parts/footer.tpl') %}
</div>
{% endblock %}
