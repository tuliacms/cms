{% extends 'backend' %}
{% trans_default_domain 'taxonomy' %}

{% block title %}
    {{ 'createTerm'|trans }}
{% endblock %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.term', { taxonomyType: taxonomyType.code }) }}">{{ 'termsListOfTaxonomy'|trans({ taxonomy: taxonomyType.name|trans({}, 'taxonomy') }) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ block('title') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.term', { taxonomyType: taxonomyType.code }) }}" class="btn btn-secondary btn-icon-only" data-bs-toggle="tooltip" title="{{ 'cancel'|trans({}, 'messages') }}"><i class="btn-icon fas fa-times"></i></a>
                <a href="#" data-submit-form="{{ form.vars.id }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans({}, 'messages') }}</a>
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.foreign_locale_creation_info() }}
            {{ render_content_builder_form_layout(form) }}
        </div>
    </div>
{% endblock %}
