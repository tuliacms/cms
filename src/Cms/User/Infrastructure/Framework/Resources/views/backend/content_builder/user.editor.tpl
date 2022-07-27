{% if context.layout|default('simple') == 'simple' %}
    {% include '@backend/user/content_builder/user.editor.simple.tpl' %}
{% else %}
    {% include '@backend/user/content_builder/user.editor.admin.tpl' %}
{% endif %}
