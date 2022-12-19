{% macro page_header(context) %}
    {% set userDetailsForm = context.userDetailsForm.createView %}

    {{ form_row(userDetailsForm.id) }}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(userDetailsForm.email, { attr: { autocomplete: 'off' } }) }}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro sidebar_accordion(context) %}
    {% set userDetailsForm = context.userDetailsForm.createView %}

    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-details">
            {{ 'details'|trans({}, 'users') }}
        </div>
        <div id="form-collapse-sidebar-details" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(userDetailsForm.name) }}
                {{ form_row(userDetailsForm.locale) }}
            </div>
        </div>
    </div>
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-avatar">
            {{ 'userAvatar'|trans({}, 'users') }}
        </div>
        <div id="form-collapse-sidebar-avatar" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(userDetailsForm.avatar) }}
                {% if userDetailsForm.avatar.vars.data %}
                    {{ form_row(userDetailsForm.remove_avatar) }}
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}

{% macro page_tabs(context) %}
    <li class="nav-item">
        <a href="#" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-security">{{ 'security'|trans }}</a>
    </li>
{% endmacro %}

{% macro page_tabs_content(context) %}
    {% set userDetailsForm = context.userDetailsForm.createView %}

    <div class="tab-pane fade show active" id="tab-security">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(userDetailsForm.password) }}
                    {% include '@backend/user/user/parts/password-complexity.tpl' %}
                    {{ form_row(userDetailsForm.enabled) }}
                    {{ form_row(userDetailsForm.roles) }}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}
