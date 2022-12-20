{% macro page_header(form, context) %}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    {{ form_row(form.name, { attr: { autofocus: 'true' } }) }}
                </div>
                <div class="col-6">
                    {{ form_row(form.space) }}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro sidebar_accordion(form, context) %}
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-widget-look">
            {{ 'widgetDetails'|trans({}, 'widgets') }}
        </div>
        <div id="form-collapse-widget-look" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(form.visibility) }}
                {{ form_row(form.title) }}
                {{ form_row(form.styles) }}
                {{ form_row(form.htmlClass) }}
                {{ form_row(form.htmlId) }}
            </div>
        </div>
    </div>
{% endmacro %}
