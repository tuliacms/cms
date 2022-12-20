{% extends 'backend' %}

{% block title %}
    {{ 'createUser'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.user') }}">{{ 'usersList'|trans({}, 'users') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'createUser'|trans({}, 'users') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.user') }}" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> {{ 'cancel'|trans({}, 'messages') }}</a>
                <a href="#" data-submit-form="{{ form.vars.id }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans({}, 'messages') }}</a>
            </div>
            <i class="pane-header-icon fas fas fa-users"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ render_content_builder_form_layout(form) }}
        </div>
    </div>
{% endblock %}
