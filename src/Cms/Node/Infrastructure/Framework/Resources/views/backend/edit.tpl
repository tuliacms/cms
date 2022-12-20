{% extends 'backend' %}
{% trans_default_domain 'node' %}

{% if nodeType.isRoutable %}
    {% set previewLink = node_path_from_id(node.id) %}
{% endif %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'editNode'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.node', { node_type: nodeType.code }) }}">{{ 'nodesListOfType'|trans({ type: nodeType.name|trans }) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'editNode'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.node', { node_type: nodeType.code }) }}" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> {{ 'cancel'|trans({}, 'messages') }}</a>
                <a href="#" data-submit-form="{{ form.vars.id }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'publish'|trans({}, 'messages') }}</a>
            </div>
            <i class="pane-header-icon {{ nodeType.icon }}"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.translation_missing_info(node.isTranslatedTo(app.request.locale)) }}
            {{ render_content_builder_form_layout_new(form.attributes) }}
        </div>
    </div>
{% endblock %}
