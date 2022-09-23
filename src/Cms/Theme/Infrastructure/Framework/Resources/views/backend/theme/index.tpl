{% extends 'backend' %}

{% block title %}
    {{ 'themes'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'themes'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            {% if development == true %}
                <div class="pane-buttons">
                    <a href="#" class="btn btn-success btn-icon-left" data-bs-toggle="modal" data-bs-target="#install-theme"><i class="btn-icon fas fa-cloud-upload-alt"></i> {{ 'installTheme'|trans({}, 'themes') }}</a>
                </div>
            {% endif %}
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ 'themes'|trans }}</h1>
        </div>
        <div class="pane-body">
            {% if usesDefaultTheme %}
                <div class="row">
                    <div class="col">
                        <div class="alert alert-info">
                            <strong>{{ 'defaultThemeInUse'|trans({}, 'themes') }}</strong>
                            <p class="mb-0">{{ 'youUseDefaultThemeInfo'|trans({}, 'themes') }}</p>
                        </div>
                    </div>
                </div>
            {% endif %}
            <div class="row">
                {% for item in themes %}
                    <div class="col-3">
                        <div class="card{{ theme.name == item.name ? ' bg-light' : '' }} mb-4">
                            {% if theme.name == item.name %}
                                <div class="ribbon"><span>{{ 'activeTheme'|trans({}, 'themes') }}</span></div>
                            {% endif %}
                            {% if item.thumbnail %}
                                <img src="{{ asset(item.thumbnail) }}" class="card-img-top" alt="{{ item.name }} theme thumbnail">
                            {% else %}
                                <svg class="bd-placeholder-img card-img-top" style="font-size:1.125rem;text-anchor:middle;" width="100%" height="180" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                    <title>{{ 'noThumbnailAvailable'|trans }}</title>
                                    <rect width="100%" height="100%" fill="#868e96"></rect>
                                    <text x="50%" y="50%" fill="#ddd" dy=".3em">{{ 'noThumbnailAvailable'|trans }}</text>
                                </svg>
                            {% endif %}
                            <div class="card-body">
                                <h5 class="card-title mb-0">
                                    {{ item.name }}
                                </h5>
                                {% if item.info %}
                                    <p class="card-text mt-3">{{ item.info }}</p>
                                {% endif %}
                                {% if item.parent %}
                                    <hr />
                                    <p class="text-muted m-0">{{ 'childOfTheme'|trans({ name: '<i>' ~ item.parent ~ '</i>' }, 'themes')|raw }}</p>
                                {% endif %}
                            </div>
                            <div class="card-footer py-0 pr-0 pe-0">
                                {% if theme.name == item.name %}
                                    <a href="{{ path('backend.theme.customize') }}" class="btn btn-sm btn-primary my-3">{{ 'customize'|trans({}, 'themes') }}</a>
                                {% endif %}
                                {% if development and theme.name != item.name %}
                                    <form action="{{ path('backend.theme.activate') }}" method="POST" class="d-inline-block">
                                        <input type="hidden" novalidate="novalidate" name="_token" value="{{ csrf_token('theme.activate') }}" />
                                        <input type="hidden" name="theme" value="{{ item.name }}" />
                                        <button type="submit" class="btn btn-sm btn-secondary tulia-click-page-loader my-3">{{ 'activate'|trans({}, 'themes') }}</button>
                                    </form>
                                {% endif %}
                                {% if development %}
                                    <div class="dropup d-inline-block float-end">
                                        <a href="#" class="card-link d-inline-block px-4 py-3 mt-1 text-dark" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="#" data-theme="{{ item.name }}" class="dropdown-item dropdown-item-danger dropdown-item-with-icon theme-delete-trigger" title="{{ 'uninstallTheme'|trans({}, 'themes') }}"><i class="dropdown-icon fas fa-times"></i>{{ 'uninstallTheme'|trans({}, 'themes') }}</a>
                                        </div>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
    <form action="{{ path('backend.theme.installator.uninstall') }}" method="POST" class="d-none" id="theme-uninstall-form">
        <input type="hidden" novalidate="novalidate" name="_token" value="{{ csrf_token('theme.uninstall') }}" />
        <input type="hidden" name="theme" value="" />
    </form>
    <div class="modal fade" id="install-theme" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{ form_start(installatorForm) }}
                    <div class="modal-header">
                        <h5 class="modal-title">{{ 'installTheme'|trans({}, 'themes') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{ form_row(installatorForm.theme) }}
                    </div>
                    <div class="modal-footer">
                        {{ form_row(installatorForm.cancel, { attr: { 'data-bs-dismiss': 'modal' } }) }}
                        {{ form_row(installatorForm.install) }}
                    </div>
                {{ form_end(installatorForm) }}
            </div>
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('.theme-delete-trigger').click(function () {
                let btn = $(this);

                Tulia.Confirmation.warning({
                    title: '{{ 'doYouWantToUninstallThisTheme'|trans({}, 'themes') }}'
                }).then((answer) => {
                    if (answer.value) {
                        const form = $('#theme-uninstall-form');
                        form.find('[name=theme]').val(btn.attr('data-theme'));
                        form.submit();
                    }
                });
            });
        });
    </script>
    <style>
        .card {
            position: relative;
        }
        .ribbon {
            position: absolute;
            right: -5px;
            top: -5px;
            z-index: 1;
            overflow: hidden;
            width: 89px;
            height: 89px;
            text-align: right;
        }
        .ribbon span {
            font-size: 10px;
            font-weight: bold;
            color: #FFF;
            text-transform: uppercase;
            text-align: center;
            line-height: 20px;
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
            width: 120px;
            display: block;
            background: #79A70A;
            background: linear-gradient(#2989d8 0%, #1e5799 100%);
            box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
            position: absolute;
            top: 26px;
            right: -24px;
        }
        .ribbon span:before {
            content: "";
            position: absolute;
            left: 0;
            top: 100%;
            z-index: -1;
            border-left: 3px solid #1e5799;
            border-right: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #1e5799;
        }
        .ribbon span:after {
            content: "";
            position: absolute;
            right: 0;
            top: 100%;
            z-index: -1;
            border-left: 3px solid transparent;
            border-right: 3px solid #1e5799;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #1e5799;
        }
    </style>
{% endblock %}
