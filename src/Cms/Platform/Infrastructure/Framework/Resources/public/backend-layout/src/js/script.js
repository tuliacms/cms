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

            if(self.isValidLink(a) && self.isPrevented(a) === false)
            {
                if(self.controls == self.form.serialize())
                    return;

                e.preventDefault();

                Tulia.Confirmation.warning({
                    title: 'Unsaved form!',
                    text: 'Do You want cancel form?'
                }).then(function (result) {
                    if(result.value)
                        document.location.href = a.prop('href');
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
            title: 'Are You sure?',
            text: 'This operation cannot be undone!',
            type: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }, options);

        return Tulia.Confirmation.fire(options);
    },
    confirm: function (options) {
        options = $.extend(true, {}, {
            title: 'Are You sure?',
            text: 'You really want to do this operation?',
            type: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }, options);

        return Tulia.Confirmation.fire(options);
    }
};

Tulia.Confirmation.swal = null;



if(typeof(Swal) !== 'undefined')
{
    Tulia.Info.swal =
        Tulia.Confirmation.swal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            buttonsStyling: false
        });
}




Tulia.ScrollDecider = function(breakpoint, beforeCallback, afterCallback) {
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

        this.container = $('<div aria-live="polite" aria-atomic="true" style="position:fixed;top:65px;right:15px;z-index:1000;"></div>');

        $('body').append(this.container);
    };

    this.show = function (parameters) {
        let toast = $(Tulia.Toasts.defaults.template);
        toast.find('.toast-body').text(parameters.content);
        toast.find('strong').text(parameters.headline);

        this.container.append(toast);
        toast.toast({ delay: 3000 }).toast('show');
    };

    this.init();
};

Tulia.Toasts.defaults = {
    template: '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">\
      <div class="toast-header">\
        <strong class="mr-auto"></strong>\
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">\
          <span aria-hidden="true">&times;</span>\
        </button>\
      </div>\
      <div class="toast-body"></div>\
    </div>'
};

Tulia.Toasts.instance = null;

Tulia.Toasts.success = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'success',
            headline: 'Powodzenie',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.danger = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'danger',
            headline: 'Błąd',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.warning = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'warning',
            headline: 'Uwaga',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.info = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'info',
            headline: 'Informacja',
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
            '   <div class="section-hl">Search results</div>' +
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



Tulia.ElementsActions = function (options) {
    this.options = options;
    this.root = null;

    this.init = function () {
        this.options = $.extend({}, Tulia.ElementsActions.defaults, this.options);
        this.root    = this.options.root ? this.options.root : $('body');

        for(let i in this.options.actions) {
            this.options.actions[i] = $.extend({}, Tulia.ElementsActions.actionsDefaults, this.options.actions[i]);
        }

        let self = this;

        this.root.find(this.options.selectors.selected).click(function () {
            let action = $(this).attr('data-action');

            if(! self.options.actions[action])
                return;

            let elements = self.getSelectedItems();

            if(! elements)
                return;

            let options = {
                action   : self.options.actions[action].action,
                headline : self.options.actions[action].headline,
                question : self.options.actions[action].question,
                elements : elements
            };

            if (! self.options.actions[action].confirmation) {
                return self.proceed(options);
            }

            self.prepareAndOpenModal(options);
        });

        this.root.find(this.options.selectors.single).click(function () {
            let action = $(this).attr('data-action');

            if (! self.options.actions[action]) {
                return;
            }

            let options = {
                action   : self.options.actions[action].action,
                headline : self.options.actions[action].headline,
                question : self.options.actions[action].question,
                elements : [{
                    id   : $(this).closest('tr').attr('data-element-id'),
                    name : $(this).closest('tr').attr('data-element-name')
                }]
            };

            if (! self.options.actions[action].confirmation) {
                return self.proceed(options);
            }

            self.prepareAndOpenModal(options);
        });

        let dropdown = this.root.find(this.options.selectors.selected).closest('.dropdown');

        if(dropdown.length === 0)
            return;

        let btn = dropdown.find('[data-toggle=dropdown]');
        btn.prop('disabled', 'disabled');

        $(this.options.selectors.checkbox).change(function () {
            if(self.getSelectedItems().length === 0)
            {
                btn.prop('disabled', 'disabled');
            }
            else
            {
                btn.prop('disabled', false);
            }
        });
    };

    this.getSelectedItems = function () {
        let elements = [];

        $(this.options.selectors.checkbox + ':checked').each(function () {
            elements.push({
                id   : $(this).closest('tr').attr('data-element-id'),
                name : $(this).closest('tr').attr('data-element-name')
            });
        });

        return elements;
    };

    this.prepareAndOpenModal = function (options) {
        let self  = this;
        let html  = options.question;
        let users = [];

        for (let key in options.elements) {
            users.push(options.elements[key].name)
        }

        html = html + '<br /> [ ' + users.join(', ') + ' ]';

        Tulia.Confirmation.warning({
            title: options.headline
        }).then(function (result) {
            if(result.value)
                self.proceed(options);
        });
    };

    this.proceed = function (options) {
        Tulia.PageLoader.show();

        let form = $('<form action="" method="POST"></form>');
        form.attr('action', options.action);
        form.addClass('d-none');

        for (let key in options.elements) {
            form.append('<input type="hidden" name="ids[]" value="' + options.elements[key].id + '" />');
        }

        form.appendTo('body');
        form.trigger('submit');
    };

    this.init();
};


Tulia.ElementsActions.defaults = {
    actions: {},
    root: null,
    selectors: {
        selected: '.action-element-selected',
        single  : '.action-element-single',
        checkbox: '.action-element-checkbox',
    },
};

Tulia.ElementsActions.actionsDefaults = {
    headline: null,
    question: null,
    action: null,
    confirmation: true,
};

export default Tulia;
