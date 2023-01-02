{% macro page_header(form, context) %}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(form.name) }}
                </div>
                {% if form.slug %}
                    <div class="col">
                        {{ form_row(form.slug) }}
                    </div>
                {% endif %}
                <div class="col-12">
                    {% if form.parent %}
                        {{ form_row(form.parent) }}
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro sidebar_accordion(form, context) %}
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-status">
            {{ 'publicationStatus'|trans }}
        </div>
        <div id="form-collapse-sidebar-status" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(form.visibility) }}
            </div>
        </div>
    </div>
{% endmacro %}
