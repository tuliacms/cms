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
                <div class="btn-group">
                    <a href="#" data-submit-form="{{ form.vars.id }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'publish'|trans({}, 'messages') }}</a>
                    <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-submit-form="{{ form.vars.id }}" data-submit-form-return="go-back" class="dropdown-item">{{ 'publishAndGoBack'|trans }}</a></li>
                        <li><a href="#" data-submit-form="{{ form.vars.id }}" data-submit-form-return="create-new" class="dropdown-item">{{ 'publishAndCreateNew'|trans }}</a></li>
                    </ul>
                </div>
            </div>
            <i class="pane-header-icon {{ nodeType.icon }}"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.translation_missing_info(node.isTranslatedTo(app.request.locale)) }}
            {{ render_content_builder_form_layout(form) }}
        </div>
    </div>
{% endblock %}
