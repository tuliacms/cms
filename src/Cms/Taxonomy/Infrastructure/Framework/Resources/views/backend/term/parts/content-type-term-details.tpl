{% macro page_header(context) %}
    {% set termDetailsForm = context.termDetailsForm %}

    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(termDetailsForm.name) }}
                </div>
                {% if termDetailsForm.slug is defined %}
                    <div class="col">
                        {{ form_row(termDetailsForm.slug) }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}
