{% assets ['backend'] %}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{ do_action('theme.head') }}
    <meta name="robots" content="noindex,nofollow">
    <title>{% block title %}Tulia CMS - Administration Panel login{% endblock %}</title>
    {% block head %}{% endblock %}

    <script nonce="{{ csp_nonce() }}">
        let Tulia = {};
        let bgImages = {{ bgImages|json_encode|raw }};
        let Login = function () {
            this.viewbox = null;
            this.animationTime = 300;

            this.init = function () {
                let self = this;

                $('[data-show-viewbox]').click(function (e) {
                    e.preventDefault();
                    self.showViewbox($(this).attr('data-show-viewbox'));
                });

                $('.viewbox').css('transition', this.animationTime + 'ms all');

                this.showViewbox('viewbox-login');

                $('.login-btn').click(function (e) {
                    self.showViewbox('viewbox-loader');
                });
            };

            this.showViewbox = function (id) {
                if (this.viewbox) {
                    $('#' + this.viewbox).removeClass('active');
                }

                let cont = $('#' + id);

                cont.addClass('active');

                let inputs = cont.find('.form-control-autofocus');
                let focused = false;

                inputs.each(function () {
                    if (focused) {
                        return;
                    }

                    if ($(this).val() === '') {
                        $(this).trigger('focus');
                        focused = true;
                    }
                });

                this.viewbox = id;
            };

            this.init();
        };

        let prefetchImage = function () {
            let image = new Image;
            image.onload = function () {
                $('.background-image').css('background-image', 'url(' + this.src + ')').addClass('active');
            };

            let modificator = (new Date).getTime();
            let randomImage = bgImages[Math.floor(Math.random() * bgImages.length)];
            image.src = randomImage.path + '?ts=' + modificator;
        };

        prefetchImage();

        $(function () {
            new Login;
        });
    </script>
</head>
<body class="login-page">
{% block beforebody %}{% endblock %}
<div class="body-container">
    <div class="left-side">
        <div class="slogan">{{ 'unleashYourCreativity'|trans({}, 'auth') }}</div>
        <div class="background-image"></div>
        <div class="image-copyrights">Images by <a href="https://unsplash.com/" target="_blank" rel="noopener noreferer">unsplash.com</a></div>
    </div>
    <div class="right-side">
        <div class="vertical-centered-box viewbox content-loader" id="viewbox-loader">
            <div class="content-loader-inner">
                <div class="content-loader-circle"></div>
                <div class="content-loader-line-mask"><div class="content-loader-line"></div></div>
                <svg class="content-loader-logo" width="60" height="60" xmlns="http://www.w3.org/2000/svg" version="1.1" xml:space="preserve"><g stroke="null"><path stroke="null" class="st0" d="m55.84563,5.5l-51.77311,0l-4.07252,7.02239l25.6376,43.72672l8.63825,0l25.6376,-43.72672l-4.06782,-7.02239zm-42.10147,13.45293l4.11949,0l15.86732,27.06087l21.49932,-37.11299l2.09967,3.61688l-23.67885,40.38697l-19.90695,-33.95174zm6.71237,0l19.26343,0l-9.62937,16.42629l-9.63406,-16.42629zm-15.09697,-11.21704l47.96364,0l-19.60163,33.839l-2.33923,-3.98797l15.35532,-26.18718l-43.50595,0l2.12786,-3.66386zm-2.11846,5.89975l39.59314,0l-1.80844,3.0814l-31.18036,0l21.87041,37.29618l-4.79589,0l-23.67885,-40.37758z" fill="#0076F8" id="svg_1"/><g stroke="null" id="svg_2"/></g></svg>
            </div>
        </div>
        <div class="vertical-centered-box viewbox" id="viewbox-login">
            <div class="centered-element">
                <div class="box">
                    <div class="box-body">
                        <div class="logo">
                            <img class="logo-image" src="{{ asset('/assets/core/backend/theme/images/logo-reverse.svg') }}" alt="Tulia CMS" />
                        </div>
                        <p class="logo-slogan">{{ 'loginToAdminPanel'|trans({}, 'auth') }}</p>
                        {{ flashes() }}
                        {% if error %}
                            <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'auth') }}</div>
                        {% endif %}
                        <form method="POST" action="{{ path('backend.login') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token('authenticate') }}" />
                            <fieldset class="form-group mb-3">
                                <label class="d-none">{{ 'username'|trans }}</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-autofocus" id="username" name="_username" value="{{ username }}" placeholder="{{ 'username'|trans }}" />
                                </div>
                            </fieldset>
                            <fieldset class="form-group mb-3">
                                <label class="d-none">{{ 'password'|trans }}</label>
                                <div class="input-group m-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control form-control-autofocus" id="password" name="_password" value="{{ password }}" placeholder="{{ 'password'|trans }}" />
                                </div>
                            </fieldset>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check mb-5 py-1">
                                        <input class="form-check-input" type="checkbox" value="" id="remember-me-checkbox" name="_remember_me">
                                        <label class="form-check-label" for="remember-me-checkbox">
                                            {{ 'rememberMe'|trans }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col text-right">
                                    <div class="btn-group float-end">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle text-uppercase" style="padding: 0 10px;" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                            {{ current_website().locale.language }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ path('backend.homepage', {_locale: 'en_US'}) }}">English (United States)</a></li>
                                            <li><a class="dropdown-item" href="{{ path('backend.homepage', {_locale: 'pl_PL'}) }}">Polski (Polska)</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}">
                            <button type="submit" class="btn btn-primary login-btn">{{ 'signIn'|trans }}</button>
                            <a href="#" class="btn btn-link" data-show-viewbox="viewbox-password-remember">{{ 'didYouForgotPassword'|trans({}, 'auth') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="vertical-centered-box viewbox" id="viewbox-password-remember">
            <div class="centered-element">
                <div class="box">
                    <div class="box-body">
                        <div class="logo">
                            <img class="logo-image" src="{{ asset('/assets/core/backend/theme/images/logo-reverse.svg') }}" alt="Tulia CMS" />
                        </div>
                        <p class="logo-slogan">Insert Your e-mail address to restore password.</p>
                        <fieldset class="form-group" style="margin-bottom:50px">
                            <label class="d-none">Username</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-autofocus" placeholder="Username">
                            </div>
                        </fieldset>
                        <a href="#" class="btn btn-primary login-btn">Process</a>
                        <a href="#" class="btn btn-link" data-show-viewbox="viewbox-login">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="credit-links">
            <a href="#">Created with &#10084; by Codevia Studio</a>
        </div>
    </div>
</div>
{{ do_action('theme.body') }}
{% block afterbody %}{% endblock %}
</body>
</html>
