{% extends 'backend' %}
{% assets ['masonry'] %}
{% trans_default_domain 'import_export' %}

{% block title %}
    {{ 'exporter'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.tools') }}">{{ 'tools'|trans({}, 'messages') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'exporter'|trans }}</li>
{% endblock %}

{% macro entries(collection, group, type) %}
    {% for object in collection.of(group, type) %}
        <div class="form-check">
            <input class="form-check-input toggle-all" data-toggle-all="{{ group }}-{{ type }}" type="checkbox" name="object[]" value="{{ type }}:{{ object.id }}" id="{{ group }}-{{ type }}-{{ object.id }}">
            <label class="form-check-label" for="{{ group }}-{{ type }}-{{ object.id }}">
                {{ object.name }}
            </label>
        </div>
    {% endfor %}
    <hr />
    <div class="form-check">
        <input class="form-check-input toggle-all-master" data-toggle-all="{{ group }}-{{ type }}" type="checkbox" id="{{ group }}-{{ type }}-toggle-all">
        <label class="form-check-label text-muted" for="{{ group }}-{{ type }}-toggle-all">
            {{ 'toggleAll'|trans({}, 'messages') }}
        </label>
    </div>
{% endmacro %}

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
                {% import _self as this %}
                <div class="row" data-masonry='{"percentPosition": true }'>
                    {% for type in collection.types %}
                        {% set groups = collection.groupsOfType(type) %}
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card mb-4">
                                {% if groups|length == 1 %}
                                    <h5 class="card-header py-3">{{ groups[0] }}</h5>
                                {% else %}
                                    <div class="card-header pb-0">
                                        <ul class="nav nav-tabs card-header-tabs mb-0">
                                            {% for group in groups %}
                                                <li class="nav-item">
                                                    <button class="nav-link{{ loop.index0 == 0 ? ' active' : '' }}" type="button" data-bs-toggle="tab" data-bs-target="#export-{{ type }}-{{ group }}">{{ group }}</button>
                                                </li>
                                            {% endfor %}
                                        </ul>
                                    </div>
                                {% endif %}
                                <div class="card-body">
                                    {% if groups|length == 1 %}
                                        {{ this.entries(collection, groups[0], type) }}
                                    {% else %}
                                        <div class="tab-content" id="myTabContent">
                                            {% for group in groups %}
                                                <div class="tab-pane fade{{ loop.index0 == 0 ? ' show active' : '' }}" id="export-{{ type }}-{{ group }}" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                                    {{ this.entries(collection, group, type) }}
                                                </div>
                                            {% endfor %}
                                        </div>
                                    {% endif %}
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
