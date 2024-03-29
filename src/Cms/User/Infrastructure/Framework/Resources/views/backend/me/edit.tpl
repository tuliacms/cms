{% extends '@backend/user/me/layout/base.tpl' %}

{% set activeTab = 'edit' %}

{% block title %}
    {{ 'editAccount'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.me') }}">{{ 'myAccount'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'editAccount'|trans({}, 'users') }}</li>
{% endblock %}

{% block page_buttons %}
    <a href="#" data-submit-form="{{ form.vars.id }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans({}, 'messages') }}</a>
{% endblock %}

{% block mainContent %}
    <div class="form-controls-terminator">
        {{ render_content_builder_form_layout(form) }}
    </div>
{% endblock %}
