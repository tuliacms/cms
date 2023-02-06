{% extends 'backend' %}
{% assets ['masonry'] %}
{% trans_default_domain 'import_export' %}

{% block title %}
    {{ 'exporter'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'exporter'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="#" data-submit-form="form-import-objects" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-file-export"></i> {{ 'doExport'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-file-export"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <form method="POST" action="{{ path('backend.import_export.exporter.export') }}" id="form-import-objects">
                <input type="hidden" value="{{ csrf_token('import-export-export-file') }}" name="_token" />
                <div class="alert alert-info">
                    <p class="mb-0">{{ 'exporterBasicInfo'|trans }}</p>
                </div>
                <div class="row" data-masonry='{"percentPosition": true }'>
                    {% for type in collection.types %}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card mb-4">
                                <h5 class="card-header py-3">{{ type }}</h5>
                                <div class="card-body">
                                    {% for object in collection.ofType(type) %}
                                        <div class="form-check">
                                            <input class="form-check-input toggle-all" data-toggle-all="{{ type }}" type="checkbox" name="object[]" value="{{ type }}:{{ object.id }}" id="{{ type }}-{{ object.id }}">
                                            <label class="form-check-label" for="{{ type }}-{{ object.id }}">
                                                {{ object.name }}
                                            </label>
                                        </div>
                                    {% endfor %}
                                    <hr />
                                    <div class="form-check">
                                        <input class="form-check-input toggle-all-master" data-toggle-all="{{ type }}" type="checkbox" id="{{ type }}-toggle-all">
                                        <label class="form-check-label text-muted" for="{{ type }}-toggle-all">
                                            {{ 'toggleAll'|trans({}, 'messages') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </form>
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('.toggle-all-master').click(function () {
                $('.toggle-all[data-toggle-all="' + $(this).attr('data-toggle-all') + '"]').prop('checked', this.checked);
            });
        });
    </script>
{% endblock %}
