{% macro page_header(context) %}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    {{ form_row(context.form.name, { attr: { autofocus: 'true' } }) }}
                </div>
                <div class="col-6">
                    {{ form_row(context.form.space) }}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro sidebar_accordion(context) %}
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-widget-look">
            {{ 'widgetDetails'|trans({}, 'widgets') }}
        </div>
        <div id="form-collapse-widget-look" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(context.form.visibility) }}
                {{ form_row(context.form.title) }}
                {{ form_row(context.form.styles) }}
                {{ form_row(context.form.htmlClass) }}
                {{ form_row(context.form.htmlId) }}
            </div>
        </div>
    </div>
{% endmacro %}
