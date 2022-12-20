{% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

{{ form_render.form_begin(context.form) }}

<div class="cbb-block-type-edit-panel">
    {% block content_before %}{% endblock %}

    {% for group in contentType.fieldGroups %}
        {% for field in group.fields %}
            {{ form_render.form_row(context.form, field.code, contentType) }}
        {% endfor %}
    {% endfor %}

    {% block content_after %}{% endblock %}
</div>

{{ form_render.form_end(context.form) }}
