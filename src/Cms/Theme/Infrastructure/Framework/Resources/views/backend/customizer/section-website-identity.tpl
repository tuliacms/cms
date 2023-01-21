{% assets ['filemanager'] %}
<div class="control-pane control-pane-name-website-identity" data-section="website-identity">
    <div class="control-pane-headline">
        <button type="button" class="control-pane-back" data-show-pane="home">
            <i class="icon fas fa-chevron-left"></i>
        </button>
        <h4>{{ 'websiteIdentity'|trans({}, 'customizer') }}</h4>
    </div>
    <div class="control-pane-content">
        <label for="settings-website-favicon">{{ 'websiteFavicon'|trans({}, 'customizer') }}</label>
        <div class="input-group">
            <input type="text" name="settings.website_favicon" value="{{ settings.website_favicon }}" id="settings-website-favicon" class="customizer-control form-control" data-transport="postMessage" />
            <div class="input-group-append">
                <button class="btn btn-default btn-icon-only" type="button" id="settings-website-favicon-trigger">
                    <i class="btn-icon fas fa-folder-open"></i>
                </button>
            </div>
        </div>
        <span class="text-muted">{{ 'faviconSizeDescription'|trans({}, 'customizer') }}</span>
        {#{% if settings.website_favicon %}
            <br />
            <img src="{{ image_url(settings.website_favicon) }}" style="max-width:64px;max-height:64px;display:inline-block;" alt=""/>
        {% endif %}#}
    </div>
</div>
<script nonce="{{ csp_nonce() }}">
    $(function () {
        TuliaFilemanager.create({
            targetInput: '#settings-website-favicon',
            endpoint: '{{ path('backend.filemanager.endpoint') }}',
            filter: {type: 'image'},
            multiple: false,
            closeOnSelect: true,
            openTrigger: '#settings-website-favicon-trigger',
        });
    });
</script>
