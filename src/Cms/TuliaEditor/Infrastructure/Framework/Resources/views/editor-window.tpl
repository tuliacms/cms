{% extends '@backend/layout/root.tpl' %}

{% set prevent_load_backend_assets = true %}

{% assets ['tulia_editor', 'tulia_editor_extensions', 'tulia_editor_blocks', 'font_awesome'] %}
{% assets theme().config.all('asset')|keys %}
{% assets theme().config.all('tulia_editor_plugin')|keys %}

{% block body %}
    <div id="tulia-editor"></div>
    <script>
        $(function () {
            new TuliaEditor.Canvas();
        });
    </script>
{% endblock %}
