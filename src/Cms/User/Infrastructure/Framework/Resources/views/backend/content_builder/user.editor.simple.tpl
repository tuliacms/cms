{% embed '@backend/content_builder/layout/_parts/editor/form_layout.simple.tpl' %}
    {% block content_before %}
        {{ form_row(context.form.name) }}
        {{ form_row(context.form.locale) }}
        {{ form_row(context.form.avatar) }}
        {{ form_row(context.form.remove_avatar) }}
    {% endblock %}
{% endembed %}
