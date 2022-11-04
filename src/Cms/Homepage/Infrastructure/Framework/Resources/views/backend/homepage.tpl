{% extends 'backend' %}
{% assets [ 'masonry' ] %}

{% block title %}
    Dashboard
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
{% endblock %}

{% block content %}
    <div class="page-content">
        <div class="page">
            <div class="dashboard">
                <div class="masonry-content-loader loading">
                    <div class="content-loader content-loader-dark">
                        <div class="content-loader-inner">
                            <div class="content-loader-circle"></div>
                            <div class="content-loader-line-mask"><div class="content-loader-line"></div></div>
                            <svg class="content-loader-logo" width="60" height="60" xmlns="http://www.w3.org/2000/svg" version="1.1" xml:space="preserve"><g stroke="null"><path stroke="null" class="st0" d="m55.84563,5.5l-51.77311,0l-4.07252,7.02239l25.6376,43.72672l8.63825,0l25.6376,-43.72672l-4.06782,-7.02239zm-42.10147,13.45293l4.11949,0l15.86732,27.06087l21.49932,-37.11299l2.09967,3.61688l-23.67885,40.38697l-19.90695,-33.95174zm6.71237,0l19.26343,0l-9.62937,16.42629l-9.63406,-16.42629zm-15.09697,-11.21704l47.96364,0l-19.60163,33.839l-2.33923,-3.98797l15.35532,-26.18718l-43.50595,0l2.12786,-3.66386zm-2.11846,5.89975l39.59314,0l-1.80844,3.0814l-31.18036,0l21.87041,37.29618l-4.79589,0l-23.67885,-40.37758z" fill="#0076F8" id="svg_1"/><g stroke="null" id="svg_2"/></g></svg>
                        </div>
                    </div>
                </div>
                <div class="dashboard-widgets loading">
                    {{ dashboard_widgets('backend.dashboard') }}

                    {#<div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="https://analytics.google.com/analytics/web/" target="_blank" rel="noopener noreferrer"><i class="fab fa-google dropdown-icon"></i> Otwórz Google Analytics</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-cogs dropdown-icon"></i> Ustawienia</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-chart-line"></i>
                                    <div class="pane-title">Analytics</div>
                                </div>
                                <div class="pane-body p-0">
                                    <div class="statistics-widget">
                                        <div class="statistics-canvas-container">
                                            <div id="timeline-chart"></div>
                                            <div class="ct-chart ct-perfect-fourth"></div>
                                            <canvas id="myChart" width="400" height="120"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="pane-footer py-1">
                                    <p class="text-muted mb-1"><small>Google Analytics by Tulia CMS</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#" data-bs-toggle="modal" data-bs-target="#widget-tulia-news-settings"><i class="fas fa-cogs dropdown-icon"></i> Ustawienia</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-newspaper"></i>
                                    <div class="pane-title">Tulia CMS Newsroom</div>
                                </div>
                                <div class="pane-body">
                                    <div class="tulia-news-widget">
                                        <div class="news-item">
                                            <div class="news-date">2 maja, 2019</div>
                                            <a href="#">Lorem ipsum dolor sit amit, elit enim at minim veniam quis nostrud</a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">12 czerwca, 2019</div>
                                            <a href="#">Aiusmdd tempor incididunt ut labore et dolore magna elit </a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">30 października, 2019</div>
                                            <a href="#">Lorem ipsum veniam quis nostrud</a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">2 maja, 2019</div>
                                            <a href="#">Lorem ipsum dolor sit amit, consectetur eiusmdd tempor incididunt ut labore et dolore magna elit enim at minim veniam quis nostrud</a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">22 stycznia, 2019</div>
                                            <a href="#">Lorem ipsum dolor sit amit, consectetur eiusmdd tempor incididunt ut labore et dolore magna elit enim at minim veniam quis nostrud</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="pane-footer py-1">
                                    <p class="mb-1"><small><a href="#" class="text-muted">Blog Tulia CMS &nbsp; <i class="fas fa-external-link-square-alt"></i></a></small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-comments"></i>
                                    <div class="pane-title">Latest comments</div>
                                </div>
                                <div class="pane-body p-0">
                                    <div class="last-comments-widget">
                                        <div class="comments-list">
                                            <div class="comment-item">
                                                <div class="comment-short-info">
                                                    <span class="comment-author">Adam Banaszkiewicz</span>
                                                    - <span class="comment-date">2019-08-25 13:54</span>,
                                                    na stronie <a href="#">Lorem ipsum dolor sit amet...</a>
                                                </div>
                                                <div class="comment-content">
                                                    Vivamus elementum tortor justo, in hendrerit augue auctor quis. Sed tempor fermentum risus, in sagittis purus gravida vitae. Nulla vitae condimentum dui. Nunc eros ex, pulvinar a tempus sodales.
                                                </div>
                                            </div>
                                            <div class="comment-item">
                                                <div class="comment-short-info">
                                                    <span class="comment-author">Adam Banaszkiewicz</span>
                                                    - <span class="comment-date">2019-08-25 13:54</span>,
                                                    na stronie <a href="#">Morbi accumsan auctor ultricies</a>
                                                </div>
                                                <div class="comment-content">
                                                    Maecenas tempus posuere ante ac aliquam. Morbi accumsan auctor ultricies. Cras suscipit nisl ut dolor pharetra, vitae condimentum risus fermentum.
                                                </div>
                                            </div>
                                            <div class="comment-item">
                                                <div class="comment-short-info">
                                                    <span class="comment-author">Adam Banaszkiewicz</span>
                                                    - <span class="comment-date">2019-08-25 13:54</span>,
                                                    na stronie <a href="#">enean sit amet blandit nunc. Ut fringilla ipsum id enim posuere congue.</a>
                                                </div>
                                                <div class="comment-content">
                                                    Proin dolor nibh, sodales nec tellus vitae, aliquam iaculis elit. Etiam dapibus ut quam malesuada vehicula. Aenean sit amet blandit nunc. Ut fringilla ipsum id enim posuere congue. Cras condimentum lectus porta ligula maximus dictum.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-cogs dropdown-icon"></i> Ustawienia</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-clock dropdown-icon"></i> Stwórz kopię teraz</a>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-archive dropdown-icon"></i> Otwórz listę kopii</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-archive"></i>
                                    <div class="pane-title">Backup</div>
                                </div>
                                <div class="pane-body">
                                    <div class="backup-widget">
                                        <p class="mb-0">Zaleca się wykonać kopię za: <b>2</b> dni</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>#}
                </div>
            </div>
            <div class="modal fade" id="widget-tulia-news-settings" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ustawienia wiadomości widgetu Tulia News</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <fieldset class="form-group">
                                <label>Wybierz język wiadomości</label>
                                <select class="form-control custom-select">
                                    <option value="">Polski</option>
                                    <option value="1">English</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                            <button type="button" class="btn btn-primary">Zapisz</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            let masonry = $('.dashboard-widgets').masonry({
                itemSelector: '.widget',
                percentPosition: true
            });

            masonry.on('layoutComplete', function () {
                masonry.removeClass('loading');
                $('.masonry-content-loader').removeClass('loading');
            });

            let masonryLayoutTimeout = null;
            let masonryLayoutCall = function () {
                masonry.masonry('layout');
            };
            let masonryLayoutReset = function () {
                clearTimeout(masonryLayoutTimeout);
                masonryLayoutTimeout = setTimeout(masonryLayoutCall, 100);
            };
            masonryLayoutCall();
        });
    </script>
{% endblock %}
