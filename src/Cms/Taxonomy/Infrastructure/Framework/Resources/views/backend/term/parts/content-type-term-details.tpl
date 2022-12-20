{% macro page_header(form, context) %}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(form.name) }}
                </div>
                {% if form.slug is defined %}
                    <div class="col">
                        {{ form_row(form.slug) }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}
