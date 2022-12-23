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
