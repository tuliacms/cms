{% extends '@backend/layout/root.tpl' %}

{% set prevent_load_backend_assets = true %}

{% assets ['tulia_editor', 'font_awesome'] %}
{% assets theme().config.all('asset')|keys %}
{% assets theme().config.all('tulia_editor_plugin')|keys %}

{% block body %}
    <div class="tued-preview-wrapper">
        <div class="tued-preview-overlay">
            <div class="tued-preview-edit">{{ 'editContent'|trans({}, 'tulia-editor') }}</div>
        </div>
        <div id="tulia-editor-preview"><div class="tued-empty-content">{{ 'startCreatingNewContent'|trans({}, 'tulia-editor') }}</div></div>
    </div>
    <style>
        body {overflow: hidden !important;}
    </style>
{% endblock %}
