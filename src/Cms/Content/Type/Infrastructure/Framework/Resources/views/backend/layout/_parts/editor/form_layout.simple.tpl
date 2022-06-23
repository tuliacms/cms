{% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

{{ form_render.form_begin(form) }}

<div class="cbb-block-type-edit-panel">
    {% for group in contentType.fieldGroups %}
        {% if group.section == 'main' %}
            {% for field in group.fields %}
                {{ form_render.form_row(form, field, contentType) }}
            {% endfor %}
        {% endif %}
    {% endfor %}
</div>

{{ form_render.form_end(form) }}
