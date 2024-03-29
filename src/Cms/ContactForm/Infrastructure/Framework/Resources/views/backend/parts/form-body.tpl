{% assets ['tulia.contact_forms'] %}

{%- macro renderRequiredAttributes(attributes) -%}
    {%- for name, attr in attributes -%}
        {%- if not (attr.required is defined and attr.required) -%}
            <span style="color:#999">
            {%- set tooltip = '' -%}
        {%- else -%}
            {%- set tooltip = ('required'|trans) ~ ': ' -%}
        {%- endif -%}
        &nbsp;<span data-bs-toggle="tooltip" title="{{ tooltip }}{{ attr.name }}">{{ name }}=""</span>
        {%- if not (attr.required is defined and attr.required) -%}
            </span>
        {%- endif -%}
    {%- endfor -%}
{%- endmacro -%}

{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}

{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

<div class="page-form">
    <div class="page-form-sidebar">
        <div class="accordion">
            {% if form.id.vars.value %}
                <div class="accordion-section">
                    <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-receivers">
                        {{ 'shortcode'|trans }}
                    </div>
                    <div class="collapse show">
                        <div class="accordion-section-body">
                            <p>{{ 'shortcodeToInsertOnPage'|trans }}</p>
                            <textarea class="form-control">[contact_form id="{{ form.id.vars.value }}"]</textarea>
                        </div>
                    </div>
                </div>
            {% endif %}
            <div class="accordion-section">
                <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-receivers">
                    {{ 'receivers'|trans({}, 'contact-form') }}
                    {{ badge.errors_count(form, [ 'receivers' ]) }}
                </div>
                <div id="form-collapse-receivers" class="collapse show">
                    <div class="accordion-section-body pb-0">
                        {{ form_row(form.receivers) }}
                    </div>
                </div>
            </div>
            <div class="accordion-section">
                <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sender">
                    {{ 'sender'|trans({}, 'contact-form') }}
                    {{ badge.errors_count(form, [ 'sender_name', 'sender_email', 'reply_to' ]) }}
                </div>
                <div id="form-collapse-sender" class="collapse show">
                    <div class="accordion-section-body pb-2">
                        {{ form_row(form.sender_name) }}
                        {{ form_row(form.sender_email) }}
                        {{ form_row(form.reply_to) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        {{ form_row(form.name, { attr: { autofocus: 'autofocus' } }) }}
                    </div>
                    <div class="col-6">
                        {{ form_row(form.subject) }}
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs page-form-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-fields">
                    {{ 'fieldsBuilder'|trans({}, 'contact-form') }}
                    {{ badge.errors_count(form, [ 'fields_template' ]) }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-message">
                    {{ 'messageTemplate'|trans({}, 'contact-form') }}
                    {{ badge.errors_count(form, [ 'message_template' ]) }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-fields">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div id="contact-form-builder"></div>
                        </div>
                    </div>
                </div>
                <style>
                    .contact-form-fields-builder {
                        margin-bottom: 30px;
                        font-size: 15px;
                    }
                    .contact-form-fields-builder .field-remove {
                        transition: .12s all;
                    }
                    .contact-form-fields-builder .field-add-to-template {
                        transition: .12s all;
                        color: green;
                        opacity: .7;
                    }
                    .contact-form-fields-builder .field-remove:hover {
                        cursor: pointer;
                        color: red;
                    }
                    .contact-form-fields-builder .field-add-to-template:hover {
                        cursor: pointer;
                        color: green;
                    }
                    .contact-form-fields-builder .form-field-prototype {
                        font-family: monospace;
                    }
                    .contact-form-fields-builder .form-field-prototype label {
                        display: inline;
                    }
                    .contact-form-fields-builder .form-field-prototype label:hover {
                        cursor: pointer;
                    }
                    .contact-form-fields-builder .form-field-prototype .field-optional {
                        color: #999;
                    }
                    .contact-form-fields-builder .form-field-prototype .form-control {
                        display: inline;
                        width: 0;
                        min-width: 0;
                        min-height: 0;
                        height: 25px;
                        line-height: 22px;
                        border: 1px solid transparent;
                        padding: 0;
                        text-align: center;
                    }
                    .contact-form-fields-builder .form-field-prototype .form-control:hover {
                        border: 1px solid #ced4da;
                        outline: none !important;
                        box-shadow: none;
                    }
                    .contact-form-fields-builder .form-field-prototype .form-control:focus {
                        border: 1px solid #ced4da;
                        width: 10px;
                        min-width: 10px;
                        outline: none !important;
                        box-shadow: none;
                    }
                    .form-field-option-legends .form-field-option-legend {
                        display: none;
                        margin-top: 20px;
                    }
                </style>
                <script nonce="{{ csp_nonce() }}">
                    window.ContactFormBuilder = {
                        fields: {{ fields|json_encode|raw }},
                        availableFields: {{ availableFields|json_encode|raw }},
                        fieldsTemplate: {{ {
                            value: form.fields_template.vars.value,
                            error: form.fields_template.vars.errors[0].message|default(null)
                        }|json_encode|raw }},
                        translations: {
                            fieldsBuilder: '{{ 'fieldsBuilder'|trans({}, 'contact-form') }}',
                            fieldsBuilderInfo: '{{ 'fieldsBuilderInfo'|trans({}, 'contact-form') }}',
                            availableFields: '{{ 'availableFields'|trans({}, 'contact-form') }}',
                            availableFieldsInfo: '{{ 'availableFieldsInfo'|trans({}, 'contact-form') }}',
                            addAnyFieldsToCreateForm: '{{ 'addAnyFieldsToCreateForm'|trans({}, 'contact-form') }}',
                            controlOptionLabel: '{{ 'controlOptionLabel'|trans({}, 'contact-form') }}',
                            valuesSeparatedByPipeAllowedFollowing: '{{ 'valuesSeparatedByPipeAllowedFollowing'|trans({}, 'contact-form') }}',
                            fieldsTemplate: '{{ 'fieldsTemplate'|trans({}, 'contact-form') }}',
                            fieldsTemplateInfo: '{{ 'fieldsTemplateInfo'|trans({}, 'contact-form') }}',
                            removeField: '{{ 'removeField'|trans({}, 'contact-form') }}',
                            addFieldToTemplate: '{{ 'addFieldToTemplate'|trans({}, 'contact-form') }}',
                            name: '{{ 'name'|trans }}',
                            required: '{{ 'required'|trans }}',
                            multilingual: '{{ 'multilingual'|trans }}',
                            yes: '{{ 'yes'|trans }}',
                            type: '{{ 'type'|trans }}',
                            add: '{{ 'add'|trans }}',
                        }
                    };
                </script>
            </div>
            <div class="tab-pane fade" id="tab-message">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            {{ form_row(form.message_template, { attr: { style: 'height:300px;font-family:monospace;font-size:15px;' } }) }}
                        </div>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ 'marker'|trans }}</th>
                            <th>{{ 'description'|trans }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><code>{% verbatim %}{{ contact_form_fields() }}{% endverbatim %}</code></th>
                            <td>Renders all form fields with submitted values as table.</td>
                        </tr>
                        <tr>
                            <th><code>{% verbatim %}{{ contact_form_field('name') }}{% endverbatim %}</code></th>
                            <td>Returns one submitted value of form, by given <code>name</code>.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{ form_end(form, {'render_rest': false}) }}
