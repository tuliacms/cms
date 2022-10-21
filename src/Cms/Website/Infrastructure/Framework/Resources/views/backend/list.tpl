{% extends 'backend' %}
{% assets ['tulia_websites_builder'] %}
{% trans_default_domain 'websites' %}

{% block title %}
    {{ 'websites'|trans({}, 'websites') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'websites'|trans({}, 'websites') }}</li>
{% endblock %}

{% set currentWebsite = current_website() %}

{% block content %}
    <div id="tulia-websites-builder"></div>

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            new TuliaWebsiteBuilder('#tulia-websites-builder', {
                websites: {{ websites|json_encode|raw }},
                locales: {{ locales|json_encode|raw }},
                endpoints: {
                    newWebsite: {
                        url: '{{ path('backend.website.create') }}',
                        csrfToken: '{{ csrf_token('new_website_form') }}'
                    },
                    toggleWebsiteVisibility: {
                        url: '{{ path('backend.website.visibility.toggle') }}',
                        csrfToken: '{{ csrf_token('toggle_website_visibility') }}'
                    },
                    newLocale: {
                        url: '{{ path('backend.website.locale.add') }}',
                        csrfToken: '{{ csrf_token('add_locale_form') }}'
                    },
                },
                translations: {
                    websitesLongDescription: '{{ 'websitesLongDescription'|trans }}',
                    websites: '{{ 'websites'|trans }}',
                    websiteInactive: '{{ 'websiteInactive'|trans }}',
                    websiteInactiveHint: '{{ 'websiteInactiveHint'|trans }}',
                    defaultLocale: '{{ 'defaultLocale'|trans }}',
                    addLocale: '{{ 'addLocale'|trans }}',
                    edit: '{{ 'edit'|trans({}, 'messages') }}',
                    manageLocale: '{{ 'manageLocale'|trans }}',
                    createWebsite: '{{ 'createWebsite'|trans }}',
                    domainDevelopment: '{{ 'domainDevelopment'|trans }}',
                    domainDevelopmentHelp: '{{ 'domainDevelopmentHelp'|trans }}',
                    pathPrefix: '{{ 'pathPrefix'|trans }}',
                    pathPrefixHelp: '{{ 'pathPrefixHelp'|trans|raw }}',
                    defaultLocaleCannotBeChangedAfterCreating: '{{ 'defaultLocaleCannotBeChangedAfterCreating'|trans }}',
                    domain: '{{ 'domain'|trans }}',
                    domainHelp: '{{ 'domainHelp'|trans }}',
                    advancedOptions: '{{ 'advancedOptions'|trans }}',
                    backendPrefix: '{{ 'backendPrefix'|trans }}',
                    backendPrefixHelp: '{{ 'backendPrefixHelp'|trans }}',
                    sslMode: '{{ 'sslMode'|trans }}',
                    sslModeHelp: '{{ 'sslModeHelp'|trans }}',
                    forceSSL: '{{ 'forceSSL'|trans }}',
                    forceNonSSL: '{{ 'forceNonSSL'|trans }}',
                    allowedBothSSL: '{{ 'allowedBothSSL'|trans }}',
                    localePrefix: '{{ 'localePrefix'|trans }}',
                    localePrefixHelp: '{{ 'localePrefixHelp'|trans }}',
                    locale: '{{ 'locale'|trans }}',
                    create: '{{ 'create'|trans({}, 'messages') }}',
                    name: '{{ 'name'|trans({}, 'messages') }}',
                    cancel: '{{ 'cancel'|trans({}, 'messages') }}',
                    activity: '{{ 'activity'|trans({}, 'messages') }}',
                    active: '{{ 'active'|trans({}, 'messages') }}',
                    inactive: '{{ 'inactive'|trans({}, 'messages') }}',
                }
            });
        });
    </script>
    {#<div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.website.create') }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-globe"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 websites-list">
                {% for website in websites %}
                    <div class="col mb-4">
                        <div class="card">
                            <div class="card-body">
                                {% if not website.active %}
                                    <span
                                        data-bs-toggle="tooltip"
                                        title="{{ 'websiteInactiveHint'|trans({}, 'websites') }}"
                                        class="badge badge-secondary"
                                    >{{ 'websiteInactive'|trans({}, 'websites') }}</span>
                                {% endif %}
                                <a href="{{ path('backend.website.edit', { id: website.id }) }}">
                                    <h4 class="card-title">{{ website.name }}</h4>
                                </a>
                                <small class="text-muted">ID: {{ website.id }}</small>
                            </div>
                            <ul class="list-group list-group-flush">
                                {% for locale in website.locales %}
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center"
                                        data-name="{{ 'languageName'|trans({ code: locale.code }, 'languages') }}"
                                        data-frontend="{{ website.address(locale) }}"
                                        data-backend="{{ website.backendAddress(locale) }}"
                                        data-bs-toggle="tooltip"
                                        title="{{ 'localeCode'|trans({ code: locale.code }, 'websites') }}"
                                    >
                                        <img src="{{ asset('/assets/core/flag-icons/' ~ locale.language ~ '.svg') }}" alt="" class="website-locale-flag-icon" />
                                        {% if locale.isDefault %}<b>{% endif %}
                                            {{ 'languageName'|trans({ code: locale.code }, 'languages') }}
                                        {% if locale.isDefault %}</b><small class="text-lowercase">({{ 'defaultLocale'|trans({}, 'websites') }})</small>{% endif %}
                                    </li>
                                {% endfor %}
                            </ul>
                            <div class="card-footer py-0 pr-0">
                                <a href="{{ path('backend.website.edit', { id: website.id }) }}" class="card-link py-3 d-inline-block" title="{{ 'edit'|trans }}">{{ 'edit'|trans }}</a>
                                <a href="#" class="card-link"></a>
                                <div class="dropup d-inline-block float-end">
                                    <a href="#" class="card-link d-inline-block px-4 py-3 text-dark" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a href="#" class="dropdown-item dropdown-item-with-icon website-locale-addresses-trigger" title="{{ 'showAddresses'|trans({}, 'websites') }}"><i class="dropdown-icon fas fa-link"></i>{{ 'showAddresses'|trans({}, 'websites') }}</a>
                                        {% if is_dev_env() %}
                                            <div class="dropdown-divider"></div>
                                            <a href="#" class="dropdown-item dropdown-item-danger dropdown-item-with-icon website-delete-trigger" title="{{ 'delete'|trans }}" data-id="{{ website.id }}"><i class="dropdown-icon fas fa-times"></i>{{ 'delete'|trans }}</a>
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>

    <div class="modal fade" id="website-locale-addresses-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ 'localesUrlAddresses'|trans({}, 'websites') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal bg-danger fade" id="website-delete-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ 'deleteWebsite'|trans({}, 'websites') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ path('backend.website.delete') }}" method="POST" id="website-delete-form">
                        <input type="hidden" novalidate="novalidate" name="_token" value="{{ csrf_token('website.delete') }}" />
                        <input type="hidden" name="id" />
                        <p>{{ 'removeWebsiteDataLossInformation'|trans({}, 'websites') }}</p>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-danger" id="website-delete-submit-form">{{ 'delete'|trans }}</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .websites-list .list-group-item {
            position: relative;
            padding-left: 36px;
        }
        .websites-list .list-group-item .website-locale-flag-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            max-width: 16px;
        }
    </style>

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('.website-delete-trigger').on('click', function (e) {
                e.preventDefault();
                let id = $(this).attr('data-id');
                $('#website-delete-form input[name=id]').val(id);
                const modal = new bootstrap.Modal('#website-delete-modal');
                modal.show();
            });

            $('#website-delete-submit-form').on('click', function () {
                Tulia.Confirmation.warning().then(function (v) {
                    if (v.value) {
                        Tulia.PageLoader.show();
                        $('#website-delete-form').trigger('submit');
                    }
                });
            });

            const addressModal = new bootstrap.Modal('#website-locale-addresses-modal');

            $('.website-locale-addresses-trigger').on('click', function (e) {
                e.preventDefault();

                let body = $('#website-locale-addresses-modal .modal-body');
                body.empty();

                $(this).closest('.card').find('.list-group-item').each(function () {
                    let backend = $(this).attr('data-backend');
                    let frontend = $(this).attr('data-frontend');
                    let name = $(this).attr('data-name');

                    body.append($(
                        '<div class="card mb-2">'
                            + '<div class="card-body">'
                                + '<h5 class="card-title"><i class="fas fa-language"></i> &nbsp; ' + name + '</h5>'
                                + '<b>{{ 'panelAddress'|trans({}, 'websites') }}:</b> <a href="' + backend + '">' + backend + '</a><br />'
                                + '<b>{{ 'frontAddress'|trans({}, 'websites') }}:</b> <a href="' + frontend + '">' + frontend + '</a>'
                            + '</div>'
                        + '</div>'
                    ));
                });

                addressModal.show();
            });
        });
    </script>#}
{% endblock %}
