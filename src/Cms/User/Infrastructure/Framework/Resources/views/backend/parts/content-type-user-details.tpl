{% macro page_header(form, context) %}
    {{ form_row(form.id) }}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(form.email, { attr: { autocomplete: 'off' } }) }}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro sidebar_accordion(form, context) %}
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-details">
            {{ 'details'|trans({}, 'users') }}
        </div>
        <div id="form-collapse-sidebar-details" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(form.name) }}
                {{ form_row(form.locale) }}
            </div>
        </div>
    </div>
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-avatar">
            {{ 'userAvatar'|trans({}, 'users') }}
        </div>
        <div id="form-collapse-sidebar-avatar" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(form.avatar) }}
                {% if form.avatar.vars.data %}
                    {{ form_row(form.remove_avatar) }}
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}

{% macro page_tabs(form, context) %}
    <li class="nav-item">
        <a href="#" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-security">{{ 'security'|trans }}</a>
    </li>
{% endmacro %}

{% macro page_tabs_content(form, context) %}
    <div class="tab-pane fade show active" id="tab-security">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(form.password) }}
                    {% include '@backend/user/user/parts/password-complexity.tpl' %}
                    {{ form_row(form.enabled) }}
                    {{ form_row(form.roles) }}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}
