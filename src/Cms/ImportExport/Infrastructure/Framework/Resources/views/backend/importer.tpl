{% extends 'backend' %}
{% trans_default_domain 'import_export' %}

{% block title %}
    {{ 'importer'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.tools') }}">{{ 'tools'|trans({}, 'messages') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'importer'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-file-import"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <form action="{{ path('backend.import_export.importer.import', { return: app.request.query.get('return') }) }}" method="POST" enctype="multipart/form-data" id="submit-content-types-import">
                <input type="hidden" name="_token" value="{{ csrf_token('import-export-import-file') }}" />
                <div class="form-controls-terminator m-auto my-5">
                    <div class="alert alert-info">
                        {{ 'importingOverwriteNotification'|trans }}
                    </div>
                    <div class="mb-3">
                        <label for="importing-file" class="form-label">{{ 'selectFile'|trans({}, 'import_export') }}</label>
                        <input class="form-control" name="file" type="file" id="importing-file" />
                        <div class="form-text">{{ 'acceptedFilesTypes'|trans({}, 'import_export') }}</div>
                    </div>
                    <div class="text-right">
                        <a href="#" data-submit-form="submit-content-types-import" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-cloud-upload-alt"></i> {{ 'doImport'|trans({}, 'messages') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
{% endblock %}
