{% extends '@backend/layout/root.tpl' %}

{% set prevent_load_backend_assets = true %}

{% assets ['tulia_editor_new.editor'] %}
{% assets theme().config.all('asset')|keys %}
{% assets theme().config.all('tulia_editor_plugin')|keys %}


{% block body %}
    <div id="tulia-editor"></div>
{% endblock %}
