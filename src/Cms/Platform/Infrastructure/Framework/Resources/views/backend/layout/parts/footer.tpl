<footer>
    {#<div class="cms-status">
        <a href="https://tuliacms.org/intl/help" target="_blank" title="Need help?" rel="noopener">
            <i class="status-icon fas fa-question-circle"></i>
            <span class="status-text">Need help?</span>
        </a>
    </div>#}
    <div class="cms-version"><a href="http://tuliacms.org/" target="_blank" title="Tulia CMS" rel="noopener">Tulia CMS v. {{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}</a></div>
</footer>

<div class="search-anything-container" id="search-anything">
    <div class="search-head">
        <div class="closer"></div>
        <div class="search-input">
            <input type="text" placeholder="{{ 'startTyping'|trans }}" class="query" />
        </div>
    </div>
    <div class="search-body">
        <div class="search-info d-none">
            <div class="pane pane-lead">
                <div class="pane-header">
                    <i class="pane-header-icon fas fa-search"></i>
                    <h1 class="pane-title">{{ 'searchAnything'|trans }}</h1>
                </div>
                <div class="pane-body">
                    <div class="search-info-wrapper">
                        <div class="hl">{{ 'searchInWholeAdmin'|trans }}</div>
                        <div class="search-in-list">
                            <ul>
                                <li><i class="icn fas fa-file-powerpoint"></i> {{ 'searchInContents'|trans }}</li>
                                <li><i class="icn fas fa-folder-open"></i> {{ 'searchInTaxonomies'|trans }}</li>
                                <li><i class="icn fas fa-cogs"></i> {{ 'searchInSettings'|trans }}</li>
                                <li><i class="icn fas fa-tools"></i> {{ 'searchInTools'|trans }}</li>
                                <li><i class="icn fas fa-dice-d6"></i> {{ 'searchInSystem'|trans }}</li>
                                <li><i class="icn fas fa-question-circle"></i> {{ 'searchInHelp'|trans }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="search-results-wrapper d-none">
            <div class="pane pane-lead">
                <div class="pane-header">
                    <i class="pane-header-icon tsa-loading-show fas fa-circle-notch fa-spin d-none"></i>
                    <i class="pane-header-icon tsa-loading-hide fas fa-search"></i>
                    <h1 class="pane-title">{{ 'searchResultsForQuery'|trans({ query: '<span class="tsa-query-preview"></span>' })|raw }}</h1>
                </div>
                <div class="pane-body">
                    <div class="search-results"></div>
                    <div class="search-loader">
                        {{ 'searchingInProgress'|trans({ query: '<b><span class="tsa-query-preview"></span></b>' })|raw }}
                    </div>
                </div>
            </div>
        </div>
        <div class="search-no-results-wrapper d-none">
            <div class="pane pane-lead">
                <div class="pane-header">
                    <i class="pane-header-icon tsa-loading-show fas fa-circle-notch fa-spin d-none"></i>
                    <i class="pane-header-icon tsa-loading-hide fas fa-search"></i>
                    <h1 class="pane-title">{{ 'searchResultsForQuery'|trans({ query: '<span class="tsa-query-preview"></span>' })|raw }}</h1>
                </div>
                <div class="pane-body">
                    No results. Try to find using whole words.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tuliacms-found-bug-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ 'didYouFoundBug'|trans }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdxaicfnJMJNa4V_kbfqL6Zcuuz9syW5r4EGA4g8t0nx2-IJg/viewform?embedded=true" width="750" height="850" frameborder="0" marginheight="0" marginwidth="0">Ładuję…</iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
            </div>
        </div>
    </div>
</div>
