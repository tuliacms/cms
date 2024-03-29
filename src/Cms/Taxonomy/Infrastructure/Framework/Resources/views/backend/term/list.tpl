{% extends 'backend' %}

{% block title %}
    {{ 'termsListOfTaxonomy'|trans({ taxonomy: taxonomyType.name|trans({}, 'taxonomy') }, 'taxonomy') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ block('title') }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <div class="dropdown">
                    <button class="btn btn-secondary btn-icon-only" type="button" data-bs-toggle="dropdown">
                        <i class="btn-icon fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-left">
                        <h6 class="dropdown-header">{{ 'goTo'|trans({}, 'messages') }}</h6>
                        {#<div class="dropdown-divider"></div>
                        {% for tax in taxonomies %}
                            <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.term', { taxonomyType: tax.code }) }}"><i class="dropdown-icon fas fa-tags"></i> {{ tax.name|trans({}, 'taxonomy') }}</a>
                        {% endfor %}#}
                        <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.settings', { group: 'tulia_taxonomy_' ~ taxonomyType.code }) }}"><i class="dropdown-icon fas fa-cogs"></i> {{ 'settings'|trans({}, 'messages') }}</a>
                    </div>
                </div>
                {% if taxonomyType.isHierarchical %}
                    <a href="{{ path('backend.term.hierarchy', { taxonomyType: taxonomyType.code }) }}" class="btn btn-secondary btn-icon-only" title="{{ 'hierarchy'|trans({}, 'taxonomy') }}" data-bs-toggle="tooltip"><i class="btn-icon fas fa-sitemap"></i></a>
                {% endif %}
                <a href="{{ path('backend.term.create', { taxonomyType: taxonomyType.code }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        {{ generator.generate(datatable, {
            data_endpoint: path('backend.term.datatable', { taxonomyType: taxonomyType.code }),
            pagination: false
        }) }}
    </div>
{% endblock %}
