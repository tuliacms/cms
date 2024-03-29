{% extends '@backend/user/me/layout/base.tpl' %}

{% set activeTab = 'password' %}

{% block title %}
    {{ 'changePassword'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.me') }}">{{ 'myAccount'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'changePassword'|trans({}, 'users') }}</li>
{% endblock %}

{% block page_buttons %}{{ form_row(form.save) }}{% endblock %}

{% block mainContent %}
    {{ form_start(form) }}
    {{ form_errors(form) }}
    {{ form_row(form._token) }}
    <div class="form-controls-terminator">
        {{ form_row(form.new_password) }}
        {% include '@backend/user/user/parts/password-complexity.tpl' %}
        <div class="alert alert-info" style="margin-bottom: 40px">
            {{ 'autoLogoutAfterPasswordChangeInfo'|trans({}, 'users') }}
        </div>
        {{ form_row(form.current_password) }}
    </div>
    {{ form_end(form) }}
{% endblock %}
