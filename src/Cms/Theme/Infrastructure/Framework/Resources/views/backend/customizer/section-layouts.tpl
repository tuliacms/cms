<div class="control-pane control-pane-name-layouts" data-section="layouts">
    <div class="control-pane-headline">
        <button type="button" class="control-pane-back" data-show-pane="home">
            <i class="icon fas fa-chevron-left"></i>
        </button>
        <h4>{{ 'browseLayouts'|trans({}, 'customizer') }}</h4>
    </div>
    <div class="control-pane-content">
        {% for item in predefinedChangesets %}
            <h5>{{ item.label|trans({}, item.translationDomain) }}</h5>
            {% if item.description %}
                <p>{{ item.description|trans({}, item.translationDomain) }}</p>
            {% endif %}
            <button type="button" class="btn btn-primary btn-sm customizer-predefined-changeset-apply" data-changeset-id="{{ item.id }}">{{ 'apply'|trans }}</button>
            <hr />
        {% endfor %}
        <h5>{{ 'resetCustomizerSettings'|trans({}, 'customizer') }}</h5>
        <button class="btn btn-primary btn-sm btn-icon-left" type="button" data-bs-toggle="modal" data-bs-target="#modal-customizer-reset-settings">
            <i class="btn-icon fas fa-eraser"></i> {{ 'resetCustomizerSettings'|trans({}, 'customizer') }}
        </button>
    </div>
</div>
