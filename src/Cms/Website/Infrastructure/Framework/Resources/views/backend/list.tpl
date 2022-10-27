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
                    activateWebsite: {
                        url: '{{ path('backend.website.enable') }}',
                        csrfToken: '{{ csrf_token('website.enable') }}'
                    },
                    deactivateWebsite: {
                        url: '{{ path('backend.website.disable') }}',
                        csrfToken: '{{ csrf_token('website.disable') }}'
                    },
                    deleteWebsite: {
                        url: '{{ path('backend.website.delete') }}',
                        csrfToken: '{{ csrf_token('website.delete') }}'
                    },
                    newLocale: {
                        url: '{{ path('backend.website.locale.add') }}',
                        csrfToken: '{{ csrf_token('add_locale_form') }}'
                    },
                    activateLocale: {
                        url: '{{ path('backend.website.locale.enable') }}',
                        csrfToken: '{{ csrf_token('website.locale.enable') }}'
                    },
                    deactivateLocale: {
                        url: '{{ path('backend.website.locale.disable') }}',
                        csrfToken: '{{ csrf_token('website.locale.disable') }}'
                    },
                    deleteLocale: {
                        url: '{{ path('backend.website.locale.delete') }}',
                        csrfToken: '{{ csrf_token('website.locale.delete') }}'
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
                    deleteLocale: '{{ 'deleteLocale'|trans }}',
                    createWebsite: '{{ 'createWebsite'|trans }}',
                    deleteWebsite: '{{ 'deleteWebsite'|trans }}',
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
                    activate: '{{ 'activate'|trans }}',
                    deactivate: '{{ 'deactivate'|trans }}',
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
{% endblock %}
