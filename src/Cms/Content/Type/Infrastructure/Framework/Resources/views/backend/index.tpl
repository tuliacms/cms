{% extends 'backend' %}
{% trans_default_domain 'content_builder' %}

{% macro badge_yes_no(condition) %}
    {% if condition %}
        <span class="badge badge-info">{{ 'yes'|trans({}, 'messages') }}</span>
    {% else %}
        <span class="badge badge-secondary">{{ 'no'|trans({}, 'messages') }}</span>
    {% endif %}
{% endmacro %}

{% macro content_type_element(type) %}
    {% import _self as macros %}

    <div class="card">
        <div class="card-body">
            {% if type.isInternal %}
                <h4 class="card-title">
                    {% if type.icon %}
                        <i class="{{ type.icon }}"></i>&nbsp;
                    {% endif %}
                    {{ type.name|trans({}, 'node') }}
                </h4>
            {% else %}
                <a href="{{ path('backend.content.type.content_type.edit', { code: type.code, contentType: type.type }) }}">
                    <h4 class="card-title">
                        {% if type.icon %}
                            <i class="{{ type.icon }}"></i>&nbsp;
                        {% endif %}
                        {{ type.name|trans({}, 'node') }}
                    </h4>
                </a>
            {% endif %}
            <small class="text-muted">{{ 'contentTypeCode'|trans }}: {{ type.code }}</small>
        </div>

        <ul class="list-group list-group-flush">
            {% if type.isInternal %}
                <li class="list-group-item"><i>{{ 'internalContentType'|trans }}</i></li>
            {% endif %}
            <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'isRoutable'|trans }}: {{ macros.badge_yes_no(type.isRoutable) }}</li>
            <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'isHierarchical'|trans }}: {{ macros.badge_yes_no(type.isHierarchical) }}</li>
        </ul>
        {% if type.isInternal == false %}
            <div class="card-footer py-0 pr-0">
                <a href="{{ path('backend.content.type.content_type.edit', { code: type.code, contentType: type.type }) }}" class="card-link py-3 d-inline-block" title="{{ 'edit'|trans({}, 'messages') }}">{{ 'edit'|trans({}, 'messages') }}</a>
                <a href="#" class="card-link"></a>
                <div class="dropup d-inline-block float-end">
                    <a href="#" class="card-link d-inline-block px-4 py-3 text-dark" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" data-href="{{ path('backend.content.type.content_type.delete', { code: type.code, contentType: type.type }) }}" class="dropdown-item dropdown-item-danger dropdown-item-with-icon content-type-delete-trigger" title="{{ 'delete'|trans({}, 'messages') }}" data-id="{{ type.code }}"><i class="dropdown-icon fas fa-times"></i>{{ 'delete'|trans({}, 'messages') }}</a>
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endmacro %}

{% block title %}
    {{ 'contentTypes'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'contentTypes'|trans }}</li>
{% endblock %}

{% trans_default_domain 'content_builder' %}
{% import _self as macros %}

{% block content %}
    {% for type in contentTypeCodes %}
        <div class="pane pane-lead mb-4">
            <div class="pane-header">
                {% if loop.index0 == 0 %}
                    <div class="pane-buttons">
                        {% set currentPath = path(app.request.attributes.get('_route'), app.request.attributes.get('_route_params')) %}
                        <a href="{{ path('backend.import_export.importer', { return: currentPath }) }}" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-cloud-upload-alt"></i> {{ 'import'|trans({}, 'messages') }}</a>
                    </div>
                {% endif %}
                <i class="pane-header-icon fas fa-box"></i>
                <h1 class="pane-title">{{ 'contentTypesListOf'|trans({ name: type|trans }) }}</h1>
            </div>
            <div class="pane-body">
                <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 content-type-list">
                    {% for contentType in contentTypeList %}
                        {% if contentType.type == type %}
                            <div class="col mb-4">
                                {{ macros.content_type_element(contentType) }}
                            </div>
                        {% endif %}
                    {% endfor %}
                    <div class="col mb-4">
                        <div class="card">
                            <a href="{{ path('backend.content.type.content_type.create', { contentType: type }) }}" class="content-type-create-button">
                                <div class="content-type-create-button-inner">
                                    <i class="fas fa-plus"></i>
                                    {{ 'createContentTypeOf'|trans({ name: type|trans }) }}
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}

    <form method="POST" id="content-type-remove-form" style="display:none">
        <input type="text" name="_token" value="{{ csrf_token('delete-content-type') }}" />
    </form>

    <style>
        .content-type-create-button {
            min-height: 210px;
            display: flex;
            align-items: center;
            align-content: center;
        }
        .content-type-create-button .content-type-create-button-inner {
            text-align: center;
            font-size: 15px;
            flex: 1 1 100%;
        }
        .content-type-create-button .fas {
            display: block;
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
            transition: .12s all;
        }
        .content-type-create-button:hover {
            text-decoration: none;
        }
        .content-type-create-button:hover .fas {
            color: #aaa;
        }
    </style>
    <script>
        $(function () {
            $('.content-type-delete-trigger').click(function (e) {
                e.preventDefault();
                let action = $(this).attr('data-href');

                Tulia.Confirmation
                    .warning({
                        title: '{{ 'youSureYouWantToDeleteContentType'|trans({}, 'content_builder') }}',
                        text: '{{ 'removingContentTypeWontRemoveContentsPleaseDoThatFirst'|trans({}, 'content_builder') }}',
                    })
                    .then(function (v) {
                        if (! v.value) {
                            return;
                        }

                        Tulia.PageLoader.show();
                        $('#content-type-remove-form').attr('action', action).submit();
                    });
            });
        });
    </script>
{% endblock %}
