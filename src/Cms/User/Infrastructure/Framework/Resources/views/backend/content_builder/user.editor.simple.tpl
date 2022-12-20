{% embed '@backend/content_builder/layout/_parts/editor/form_layout.simple.tpl' %}
    {% block content_before %}
        {{ form_row(form.name) }}
        {{ form_row(form.locale) }}
        {{ form_row(form.avatar) }}
        {{ form_row(form.remove_avatar) }}
    {% endblock %}
{% endembed %}
