import './../sass/backend-theme.scss';

Tulia = Tulia ?? {};

Tulia.UI = {};
Tulia.UI.init = function () {
    if ($.fn.datetimepicker) {
        $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'far fa-arrow-up',
                down: 'far fa-arrow-down',
                previous: 'far fa-chevron-left',
                next: 'far fa-chevron-right',
                today: 'far fa-calendar-check-o',
                clear: 'far fa-trash',
                close: 'far fa-times'
            }
        });
    }

    if (Tulia.Menu) {
        let menu = new Tulia.Menu('aside .lead-menu');
    }
    if (Tulia.SearchAnything) {
        let se = new Tulia.SearchAnything('.search-anything-container');
    }
    if (Tulia.Toasts) {
        let toasts = new Tulia.Toasts();
    }

    Tulia.Form.createForEach('form');
    Tulia.PageLoader.init();

    if (typeof(SimpleBar) !== 'undefined') {
        let node = document.getElementById('notifications-scrollarea');

        if (node) {
            let scrollbar = new SimpleBar(node);

            $('.notifications-list .dropdown').on('shown.bs.dropdown', function () {
                scrollbar.recalculate();
            });
        }
    }

    $(document).on('click', '.dropdown-prevent-close .dropdown-menu', function (e) {
        e.stopPropagation();
    });

    $('.toggle-fullscreen').click(function () {
        Tulia.Fullscreen.toggle();
    });

    let body = $('body');
    let headerScroll = new Tulia.ScrollDecider(3, function () {
        body.removeClass('header-fixed');
    }, function () {
        body.addClass('header-fixed');
    });

    headerScroll.start();

    $('#mobile-menu-trigger').click(() => {
        $('body').toggleClass('main-menu-opened');
    });
    $('.mobile-menu-content-overlay').click(() => {
        $('body').removeClass('main-menu-opened');
    });

    Tulia.UI.refresh(body);
};
Tulia.UI.refresh = function (container) {
    if($.fn.chosen) {
        container
            .find('select.form-select-custom')
            .not('.ui-done-select')
            .addClass('ui-done-select')
            .chosen({
                search_contains: true,
                width: '100%',
                disable_search_threshold: 6,
            })
            .on('ui:update', function () {
                $(this).trigger('chosen:updated');
            })
        ;
    }

    if(bootstrap.Tooltip) {
        let tooltipTriggerList = [].slice.call(container.get(0).querySelectorAll('[data-bs-toggle="tooltip"]'));
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
};





Tulia.PageLoader = {
    loader: null,
    init: function () {
        this.getLoader();

        $('body').on('click', '.tulia-click-page-loader', function () {
            Tulia.PageLoader.show();
        });
    },
    show: function () {
        this.getLoader().addClass('active');
    },
    hide: function () {
        this.getLoader().removeClass('active');
    },
    getLoader: function () {
        if(this.loader)
            return this.loader;

        this.loader = $('<div class="page-loader">Please wait...</div>');
        this.loader.appendTo('body');

        return this.loader;
    }
};





Tulia.Form = function (form, options) {
    this.form = form;
    this.options = options;
    this.controls = null;

    this.init = function () {
        if(typeof(this.form) === 'string')
        {
            this.form = $(this.form);
        }

        this.bindEvents();
        this.bindSubmitters();
        this.bindLeaveUnsavedNotice();
    };

    this.bindEvents = function () {
        let self = this;

        this.form.on('tulia:form:submitted', function () {
            self.submitted();
        });
    };

    this.bindSubmitters = function () {
        let self = this;
        let selector = this.getFormId();

        $('[data-submit-form]').each(function () {
            let btnSelector = $(this).attr('data-submit-form');

            if(btnSelector === selector || btnSelector === '#' + selector)
            {
                $(this).click(function (e) {
                    e.preventDefault();
                    Tulia.PageLoader.show();

                    // Set timeout (150ms) to wait until browser done page loader animation.
                    setTimeout(function () {
                        self.form.trigger('submit');
                    }, 150);
                });
            }
        });
    };

    this.bindLeaveUnsavedNotice = function () {
        let self = this;

        this.controls = this.form.serialize();

        $('body').on('click', 'a', function (e) {
            let a = $(this);

            if (self.isValidLink(a) && self.isPrevented(a) === false) {
                if (self.controls === self.form.serialize()) {
                    return;
                }

                e.preventDefault();

                Tulia.Confirmation.warning({
                    title: 'Unsaved form!',
                    text: 'Do You want cancel form?'
                }).then(function (result) {
                    if (result.value) {
                        document.location.href = a.prop('href');
                    }
                });
            }
        });
    };

    this.submitted = function () {
        this.controls = this.form.serialize();
    };

    this.isPrevented = function (a) {
        return a.hasClass('tulia-form-prevent-confirm');
    };

    this.isValidLink = function (a) {
        let propHref = a.prop('href');
        let attrHref = a.attr('href');

        if (! propHref || ! attrHref) {
            return false;
        }

        if (attrHref === '#') {
            return false;
        }

        if (attrHref.substring(0, 1) === '#') {
            return false;
        }

        if (attrHref === 'javascript:;') {
            return false;
        }

        return true;
    };

    this.getFormId = function () {
        return this.form.attr('id');
    };

    this.init();
};

Tulia.Form.createForEach = function (selector, options) {
    $(selector).each(function () {
        new Tulia.Form($(this), options);
    });
};

Tulia.Form.defaults = {

};





Tulia.Info = {
    fire: function (options) {
        return Tulia.Info.swal.fire(options);
    },
    info: function (options) {
        if(typeof(options) === 'string')
        {
            options = {
                title: options
            }
        }

        options = $.extend(true, {}, {
            title: 'Operation done',
            type: 'info',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            focusConfirm: false,
            showCancelButton: false,
            confirmButtonText: 'Ok',
        }, options);

        return Tulia.Info.fire(options);
    },
    success: function (options) {
        if(typeof(options) === 'string')
        {
            options = {
                title: options
            }
        }

        options = $.extend(true, {}, {
            title: 'Operation done',
            type: 'success',
            customClass: {
                confirmButton: 'btn btn-success'
            },
            focusConfirm: false,
            showCancelButton: false,
            confirmButtonText: 'Ok',
        }, options);

        return Tulia.Info.fire(options);
    }
};

Tulia.Info.swal = null;







Tulia.Confirmation = {
    fire: function (options) {
        return Tulia.Confirmation.swal.fire(options);
    },
    warning: function (options) {
        options = $.extend(true, {}, {
            title: Tulia.trans('areYouSure'),
            text: Tulia.trans('thisOperationCannotBeUndone'),
            type: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: Tulia.trans('yes'),
            cancelButtonText: Tulia.trans('no'),
        }, options);

        return Tulia.Confirmation.fire(options);
    },
    confirm: function (options) {
        options = $.extend(true, {}, {
            title: Tulia.trans('areYouSure'),
            text: Tulia.trans('youReallyWantToDoThis'),
            type: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: Tulia.trans('yes'),
            cancelButtonText: Tulia.trans('no'),
        }, options);

        return Tulia.Confirmation.fire(options);
    },
    critical: function (options) {
        options = $.extend(true, {}, {
            title: Tulia.trans('confirmationRequired'),
            text: Tulia.trans('typeYourPasswordToConfirm'),
            type: null,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: Tulia.trans('yes'),
            cancelButtonText: Tulia.trans('no'),
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                return fetch(Tulia.Globals.password_protection.endpoint + `?password=${password}`)
                    .then(response => {
                        if (!response.ok) {
                            Swal.showValidationMessage(Tulia.trans('invalidPassword'));
                            return false;
                        }

                        return { value: true, password: password };
                    })
                    .catch(error => Swal.showValidationMessage(Tulia.trans('invalidPassword')))
            },
            allowOutsideClick: () => !Swal.isLoading()
        }, options);

        return Tulia.Confirmation.fire(options);
    }
};

Tulia.Confirmation.swal = null;



if (typeof(Swal) !== 'undefined') {
    Tulia.Info.swal =
        Tulia.Confirmation.swal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            buttonsStyling: false
        });
}




Tulia.ScrollDecider = function (breakpoint, beforeCallback, afterCallback) {
    this.breakpoint         = breakpoint;
    this.beforeCallback     = beforeCallback;
    this.afterCallback      = afterCallback;
    this.isBeforeBreakpoint = false;

    this.start = function() {
        var self = this;

        $(window).scroll(function() {
            self.decide();
        });

        self.decide();
    };

    this.decide = function() {
        if(this.isBeforeBreakpoint)
        {
            if($(window).scrollTop() < this.breakpoint)
            {
                this.isBeforeBreakpoint = false;
                this.beforeCallback();
            }
        }
        else
        {
            if($(window).scrollTop() > this.breakpoint)
            {
                this.isBeforeBreakpoint = true;
                this.afterCallback();
            }
        }
    };
};






Tulia.Fullscreen = {
    status: false,
    element: document.documentElement,
    toggle: function () {
        this.status ? this.off() : this.on();
    },
    on: function () {
        if (this.element.requestFullscreen)
            this.element.requestFullscreen();
        else if (this.element.mozRequestFullScreen)
            this.element.mozRequestFullScreen();
        else if (this.element.webkitRequestFullscreen)
            this.element.webkitRequestFullscreen();
        else if (this.element.msRequestFullscreen)
            this.element.msRequestFullscreen();

        this.status = true;
    },
    off: function () {
        if (document.exitFullscreen)
            document.exitFullscreen();
        else if (document.mozCancelFullScreen)
            document.mozCancelFullScreen();
        else if (document.webkitExitFullscreen)
            document.webkitExitFullscreen();
        else if (document.msExitFullscreen)
            document.msExitFullscreen();

        this.status = false;
    }
};








Tulia.Menu = function (selector) {
    this.selector = selector;
    this.menu     = null;

    this.init = function () {
        this.menu = $(this.selector);

        let self = this;

        this.menu.find('li.has-dropdown > a').click(function (e) {
            if ($(this).next('ul').length) {
                e.preventDefault();
                $(this).parent().addClass('animated').toggleClass('dropdown-opened');

                self.updateOpenedDropdownInStorage();
            }
        });
    };

    this.updateOpenedDropdownInStorage = function () {
        let ids = [];

        this.menu.find('.dropdown-opened').each(function () {
            if ($(this).find('> ul').length) {
                ids.push($(this).attr('data-item-id'));
            }
        });

        Cookies.set('aside-menuitems-opened', ids.join('|'));
    };

    this.init();
};










Tulia.Toasts = function () {
    this.container = null;

    this.init = function () {
        Tulia.Toasts.instance = this;

        this.container = $('<div aria-live="polite" aria-atomic="true" style="position:fixed;top:15px;right:15px;z-index:1000;"></div>');

        $('body').append(this.container);
    };

    this.show = function (parameters) {
        let toast = $(Tulia.Toasts.defaults.template);
        toast.find('.toast-body').text(parameters.content);
        toast.find('strong').text(parameters.headline);
        toast.find('small.text-muted').text(Tulia.trans('justNow'));

        switch (parameters.theme) {
            case 'success':
                toast.find('i').addClass('fa-solid fa-check bg-success');
                break;
            case 'danger':
                toast.find('i').addClass('fa-solid fa-circle-exclamation bg-danger');
                break;
            case 'warning':
                toast.find('i').addClass('fa-solid fa-triangle-exclamation bg-warning');
                break;
            case 'info':
                toast.find('i').addClass('fa-solid fa-circle-info bg-info');
                break;
        }

        this.container.append(toast);
        (new bootstrap.Toast(toast.get(0), {delay: 3000})).show();
    };

    this.init();
};

Tulia.Toasts.defaults = {
    template: '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">\
    <div class="toast-header">\
        <i class="me-2 text-white rounded px-1 py-1"></i>\
        <strong class="me-auto" style="font-size:14px;"></strong>\
        <small class="text-muted text-lowercase">just now</small>\
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>\
    </div>\
    <div class="toast-body" style="font-size:14px;"></div>\
</div>'
};

Tulia.Toasts.instance = null;

Tulia.Toasts.success = function (parameters) {
    if (typeof parameters == 'string') {
        parameters = {
            theme: 'success',
            headline: Tulia.trans('success'),
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.danger = function (parameters) {
    if (typeof parameters == 'string') {
        parameters = {
            theme: 'danger',
            headline: Tulia.trans('danger'),
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.warning = function (parameters) {
    if (typeof parameters == 'string') {
        parameters = {
            theme: 'warning',
            headline: Tulia.trans('warning'),
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.info = function (parameters) {
    if (typeof parameters == 'string') {
        parameters = {
            theme: 'info',
            headline: Tulia.trans('info'),
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};











Tulia.SearchAnything = function (selector, options) {
    this.selector  = selector;
    this.options   = options;
    this.container = null;
    this.template  = null;
    this.query     = null;
    this.loading   = false;
    this.results   = [];
    this.debounceSearch = null;
    this.elm = {
        query: null,
        intro: null,
        noResults: null,
        results: null
    };

    this.init = function () {
        this.container = $(this.selector);
        this.options   = $.extend({}, Tulia.SearchAnything.defaults, this.options);

        let self = this;

        $(this.options.trigger).click(function () {
            self.open();
        });

        $('body').keydown(function (e) {
            if (self.isOpened() && e.which === 27) {
                self.close();
            }
        });

        this.debounceSearch = _.debounce(self.search, 500);

        this.createView();
    };

    this.search = function () {
        let self = this;

        this.results = [];
        this.elm.searchResults.empty();
        self.hideIntro();
        self.hideEmptyResults();
        self.showLoader();

        $.ajax({
            url: this.options.endpoint,
            data: {
                q: this.query
            },
            dataType: 'json',
            success: function (data) {
                self.hideLoader();

                if (!data || data.length === 0) {
                    self.showEmptyResults();
                    self.hideResults();
                } else {
                    self.showResults();
                    self.render(data);
                }
            },
            error: function () {
                self.hideLoader();
                self.showEmptyResults();
            }
        });
    };

    this.render = function (results) {
        let html = '<div class="search-result-group">' +
            '<i class="section-icon fas fa-search"></i>' +
            '   <div class="section-hl">' + Tulia.trans('searchResults', 'search_anything') + '</div>' +
            '   <div class="result-links">';

        for (let i in results) {
            let hit = results[i];
            let tags = '';

            if (hit.tags && hit.tags.length) {
                tags = '<div class="link-tags">';
                for (let t in hit.tags) {
                    tags += '<span class="link-tag"><i class="link-tag-icon ' + hit.tags[t].icon + '"></i> ' + hit.tags[t].tag + '</span>';
                }
                tags += '</div>';
            }

            html += '<a class="result-link ' + (hit.image ? 'has-image' : '') + '" href="' + hit.link +'">' +
                (hit.image ? '<div class="link-image"><div class="link-image-item" style="background-image:url(' + hit.image + ')"></div></div>' : '') +
                '    <div class="link-details">' +
                '        <div class="link-head">' +
                '            <span class="link-label" title="' + hit.title + '">' +
                '                ' + hit.title +
                '            </span>' +
                '        </div>' +
                '        <div class="link-body">' +
                (hit.description ? '<div class="link-description">' + hit.description + '</div>' : '') +
                '            ' + tags +
                '        </div>' +
                '    </div>' +
                '</a>';
        }

        html += '</div></div>';

        this.elm.searchResults.html(html);
    };

    this.resetView = function () {
        this.elm.intro.removeClass('d-none');
        this.elm.results.addClass('d-none');
        this.elm.noResults.addClass('d-none');
        this.hideLoader();
    };

    this.hideIntro = function () {
        this.elm.intro.addClass('d-none');
    };

    this.showLoader = function () {
        this.showResults();
        this.container.find('.tsa-loading-show').removeClass('d-none');
        this.container.find('.tsa-loading-hide').addClass('d-none');
        this.elm.searchResults.addClass('d-none');
        this.elm.searchLoader.removeClass('d-none');
    };

    this.hideLoader = function () {
        this.container.find('.tsa-loading-show').addClass('d-none');
        this.container.find('.tsa-loading-hide').removeClass('d-none');
        this.elm.searchResults.removeClass('d-none');
        this.elm.searchLoader.addClass('d-none');
    };

    this.showEmptyResults = function () {
        this.elm.noResults.removeClass('d-none');
    };

    this.hideEmptyResults = function () {
        this.elm.noResults.addClass('d-none');
    };

    this.showResults = function () {
        this.elm.results.removeClass('d-none');
    };

    this.hideResults = function () {
        this.elm.results.addClass('d-none');
    };

    this.createView = function () {
        let self = this;

        this.elm.query   = this.container.find('.query');
        this.elm.queryPreview = this.container.find('.tsa-query-preview');
        this.elm.intro   = this.container.find('.search-info');
        this.elm.results = this.container.find('.search-results-wrapper');
        this.elm.noResults = this.container.find('.search-no-results-wrapper');
        this.elm.searchResults = this.container.find('.search-results');
        this.elm.searchLoader  = this.container.find('.search-loader');

        this.container.find('.closer').click(function () {
            self.close();
        });
        this.elm.query.on('change keydown keyup', function () {
            let query = $(this).val();

            if (self.query === query) {
                return;
            }

            self.query = query;

            self.elm.queryPreview.text(query);
            self.results = [];

            if (query) {
                self.hideIntro();
                self.hideEmptyResults();
                self.showLoader();
                self.debounceSearch();
            } else {
                self.resetView();
                self.debounceSearch.cancel();
            }
        });
    };

    this.isOpened = function () {
        return this.container.hasClass('opened');
    };

    this.open = function () {
        this.resetView();
        $('body').addClass('prevent-scroll');
        this.container.addClass('opened');
        this.container.find('.query')
            .val('')
            .trigger('change')
            .focus();
    };

    this.close = function () {
        this.results = [];
        this.container.removeClass('opened');
        this.debounceSearch.cancel();
        $('body').removeClass('prevent-scroll');
    };

    this.init();
};

Tulia.SearchAnything.defaults = {
    trigger: '.search-area',
    endpoint: Tulia.Globals && Tulia.Globals.search_anything && Tulia.Globals.search_anything.endpoint ? Tulia.Globals.search_anything.endpoint : null
};



Tulia.Translator = {
    translations: {},
    register: function (locale, domain = 'messages', messages = {}) {
        if (!Tulia.Translator.translations.hasOwnProperty(locale)) {
            Tulia.Translator.translations[locale] = {};
        }

        if (!Tulia.Translator.translations[locale].hasOwnProperty(domain)) {
            Tulia.Translator.translations[locale][domain] = messages;
            return;
        }

        for (let i in messages) {
            Tulia.Translator.translations[locale][domain][i] = messages[i];
        }
    }
};
Tulia.trans = function (name, domain = 'messages') {
    const locale = Tulia.Globals.user.locale;

    return Tulia.Translator.translations.hasOwnProperty(locale)
        && Tulia.Translator.translations[locale].hasOwnProperty(domain)
        && Tulia.Translator.translations[locale][domain].hasOwnProperty(name)
            ? Tulia.Translator.translations[locale][domain][name]
            : name;
};

export default Tulia;
