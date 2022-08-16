{% extends 'backend' %}
{% trans_default_domain 'themes' %}

{% block title %}
    {{ 'themes'|trans({}, 'messages') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.theme') }}">{{ 'themes'|trans({}, 'messages') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'themeImports'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ 'themeImports'|trans }}</h1>
        </div>
        <div class="pane-body">
            <div class="card" style="width: 500px;max-width: 100%;margin: 0 auto;">
                <div class="card-header">
                    <p class="mb-0">{{ 'importSampleContentOrSkipToCreateContentYourself'|trans }}</p>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            {% for key, item in imports %}
                                <tr>
                                    <td>{{ item.name }}</td>
                                    <td class="text-right">
                                        <form action="{{ path('backend.theme.installator.importer.import') }}" method="POST">
                                            <input type="hidden" name="_token" value="{{ csrf_token('theme.importer.import') }}" />
                                            <input type="hidden" name="theme" value="{{ theme }}" />
                                            <input type="hidden" name="collection" value="{{ key }}" />
                                            <button type="submit" class="btn btn-success btn-sm">{{ 'doImport'|trans }}</button>
                                        </form>
                                    </td>
                                </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                    <a href="{{ path('backend.theme') }}" class="btn btn-primary btn-sm">{{ 'finish'|trans }}</a>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
