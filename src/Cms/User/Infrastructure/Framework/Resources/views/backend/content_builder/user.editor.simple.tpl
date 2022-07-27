{% embed '@backend/content_builder/layout/_parts/editor/form_layout.simple.tpl' %}
    {% block content_before %}
        {% set userDetailsForm = context.userDetailsForm.createView %}

        {{ form_row(userDetailsForm.name) }}
        {{ form_row(userDetailsForm.locale) }}
        {{ form_row(userDetailsForm.avatar) }}
        {{ form_row(userDetailsForm.remove_avatar) }}
    {% endblock %}
{% endembed %}
