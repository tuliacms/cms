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
    template: '#search-anything-template',
    endpoint: Tulia.Globals && Tulia.Globals.search_anything && Tulia.Globals.search_anything.endpoint ? Tulia.Globals.search_anything.endpoint : null
};
