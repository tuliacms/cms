{% macro page_header(context) %}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(context.form.name) }}
                </div>
                {% if context.form.slug is defined %}
                    <div class="col">
                        {{ form_row(context.form.slug) }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}
