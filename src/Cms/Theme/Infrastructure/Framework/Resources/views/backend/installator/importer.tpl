{% extends 'backend' %}
{% trans_default_domain 'themes' %}

{% block title %}
    {{ 'themes'|trans({}, 'messages') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.theme') }}">{{ 'themes'|trans({}, 'messages') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'themeImports'|trans }}</li>
{% endblock %}

{% import '@backend/theme/_parts/showreel.tpl' as showreel %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.theme') }}" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-chevron-left"></i> {{ 'finish'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ 'themeImports'|trans }}</h1>
        </div>
        <div class="pane-body">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="mb-0">{{ 'importSampleContentOrSkipToCreateContentYourself'|trans }}</p>
                </div>
            </div>
            <div class="row">
                {% for key, item in imports %}
                    <div class="col-12 col-sm-6 col-md-4 col-xxl-3">
                        <div class="card mb-4">
                            {{ showreel.showreel(theme, item.showreel) }}
                            <div class="card-body">
                                <h5 class="card-title mb-0">
                                    {{ item.name }}
                                </h5>
                            </div>
                            <div class="card-footer">
                                <form action="{{ path('backend.theme.installator.importer.import') }}" method="POST" class="text-end">
                                    <input type="hidden" name="_token" value="{{ csrf_token('theme.importer.import') }}" />
                                    <input type="hidden" name="theme" value="{{ theme }}" />
                                    <input type="hidden" name="collection" value="{{ key }}" />
                                    <button type="submit" class="btn btn-success btn-sm d-inline-block">{{ 'doImport'|trans }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>

    {{ showreel.javascript() }}
{% endblock %}
