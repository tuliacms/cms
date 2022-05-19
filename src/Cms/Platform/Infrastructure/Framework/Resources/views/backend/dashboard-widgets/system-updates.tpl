{% extends '@backend/homepage/dashboard/homepage-widget.tpl' %}

{% set title = 'systemUpdates'|trans %}
{% set icon = 'fas fa-charging-station' %}

{% block content %}
    <div class="system-update-widget">
        <div class="status-icon"><a title="{{ 'seeWhatsNewInThisVersion'|trans }}" target="_blank" rel="noopener" href="https://tuliacms.org/intl/whats-new/version/{{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}">{{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}</a></div>
        <p>{{ 'yourTuliaCmsVersion'|trans }}</p>
    </div>
{% endblock %}

{% block footer %}
    <div class="row">
        <div class="col">
            <p class="text-muted mb-1"><small>{{ 'releasedAt'|trans }} {{ constant('Tulia\\Cms\\Platform\\Version::RELEASED') }}</small></p>
        </div>
        <div class="col text-right">
            <a title="{{ 'seeWhatsNewInThisVersion'|trans }}" target="_blank" rel="noopener" href="https://tuliacms.org/intl/whats-new/version/{{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}">{{ 'seeWhatsNewInThisVersion'|trans }}</a>
        </div>
    </div>
{% endblock %}
