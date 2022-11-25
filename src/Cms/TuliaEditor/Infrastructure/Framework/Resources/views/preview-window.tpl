{% extends '@backend/layout/root.tpl' %}

{% set prevent_load_backend_assets = true %}

{% assets ['tulia_editor', 'font_awesome'] %}
{% assets theme().config.assets %}

{% block body %}
    <div class="tued-preview-wrapper">
        <div class="tued-preview-overlay">
            <div class="tued-preview-edit">{{ 'editContent'|trans({}, 'tulia-editor') }}</div>
        </div>
        <div id="tulia-editor-preview">{{ content|raw }}</div>
        <form method="POST" style="display: none !important;" id="tulia-editor-preview-form">
            <input type="hidden" name="content" value="{{ source|e }}" />
        </form>
    </div>
    <style>
        body {overflow: hidden !important;}
    </style>
{% endblock %}
