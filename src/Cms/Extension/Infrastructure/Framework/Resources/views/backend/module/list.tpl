{% extends 'backend' %}
{% assets ['momentjs'] %}
{% trans_default_domain 'extension' %}

{% block title %}
    {{ 'modules'|trans({}, 'extension') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ block('title') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-box-open"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="row">
                <div class="col-12">
                    <div class="extensions-list">
                        {% for item in modules %}
                            <div class="extension-entry">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckCheckedDisabled" checked disabled>
                                    <label class="form-check-label" for="flexSwitchCheckCheckedDisabled"></label>
                                </div>
                                <div class="extension-icon">
                                    <i class="fa-solid fa-border-none text-muted" style="font-size: 40px;"></i>
                                </div>
                                <div class="extension-name">
                                    <span class="extension-label">{{ item.details.name }}</span>
                                    <br />
                                    {{ item.details.info|default }}
                                </div>
                                <div class="extension-details text-muted">
                                    {{ 'installedAt'|trans }} <span data-date="{{ item['installed-at'] }}" title="{{ item['installed-at'] }}">{{ item['installed-at'] }}</span><br />
                                    {{ 'version'|trans }} {{ item.version }}<br />
                                    <span class="badge bg-secondary">{{ item.source }}</span>
                                </div>
                                <div class="extension-action">
                                    {#Some action#}
                                </div>
                                <div class="extension-more-action">
                                    {#More actions#}
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .extension-entry {
            padding: 10px 32px;
            display: grid;
            grid-column-gap: 32px;
            grid-template-columns: 26px 60px 4fr 2fr minmax(min-content,1fr) auto;
            align-items: center;
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 4px 0 rgb(0,0,0,.1);
            margin-bottom: 20px;
            transition: .12s all;
        }

        .extension-entry:last-child {
            margin-bottom: 0;
        }

        .extension-entry:hover {
            box-shadow: 0 0 8px 0 rgb(0,0,0,.2);
        }

        .extension-entry .extension-name .extension-label {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            font-size: 16px;
            font-weight: 600;
            color: #52667a;
        }
    </style>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('[data-date]').each(function () {
                $(this).html(moment($(this).attr('data-date')).fromNow());
            });
        });
    </script>
{% endblock %}
