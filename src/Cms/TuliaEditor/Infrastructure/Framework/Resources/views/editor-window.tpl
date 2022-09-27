{% extends '@backend/layout/root.tpl' %}

{% set prevent_load_backend_assets = true %}

{% assets ['tulia_editor', 'font_awesome'] %}
{% assets theme().config.assets %}

{% block body %}
    <div id="tulia-editor"></div>
    <script>
        $(function () {
            new TuliaEditor.Canvas();
        });
    </script>
{% endblock %}
