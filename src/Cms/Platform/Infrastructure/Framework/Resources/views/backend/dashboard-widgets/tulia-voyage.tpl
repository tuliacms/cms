{% extends '@backend/homepage/dashboard/homepage-widget.tpl' %}
{% trans_default_domain 'tulia-tour' %}

{% set title = 'tuliaVoyage'|trans %}
{% set icon = 'fa-solid fa-wand-sparkles' %}

{% if app.request.query.has('tulia-voyage') %}
    {% assets ['shepherd'] %}
{% endif %}

{% block content %}
    <div class="system-update-widget">
        <p class="text-center lead mt-4" style="font-size:26px;">{{ 'newInTuliaCMS'|trans }}</p>
        <p class="text-center" style="font-size:15px;">{{ 'youNeedTourThroughSystem'|trans }}</p>
        <div class="text-center mb-5 mt-4">
            <a href="{{ path('backend.homepage', { 'tulia-voyage': 1 }) }}" class="btn btn-icon-left btn-dark start-tuliacms-tour">
                <i class="btn-icon fa-solid fa-wand-sparkles"></i>
                {{ 'startTour'|trans }}
            </a>
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        $(() => {
            if (!window.Shepherd) {
                return;
            }

            const tour = new Shepherd.Tour({
                useModalOverlay: true,
                defaultStepOptions: {
                    cancelIcon: { enabled: true },
                    scrollTo: { behavior: 'smooth', block: 'center' }
                }
            });

            tour.addStep({
                title: '{{ 'tourStepSidebarHeadline'|trans }}',
                text: '{{ 'tourStepSidebarBody'|trans|raw }}',
                attachTo: { element: '.lead-menu', on: 'right' },
                popperOptions: {
                    modifiers: [{name: 'offset', options: {offset: [0, 10]}}],
                },
                buttons: [
                    {
                        action() { return this.next() },
                        text: '{{ 'next'|trans }}'
                    }
                ]
            });
            tour.addStep({
                title: '{{ 'tourStepSearchAnythingHeadline'|trans }}',
                text: '{{ 'tourStepSearchAnythingBody'|trans|raw }}',
                attachTo: { element: '.search-area', on: 'bottom' },
                popperOptions: {
                    modifiers: [{name: 'offset', options: {offset: [0, 12]}}],
                },
                buttons: [
                    {
                        action() { return this.back() },
                        classes: 'shepherd-button-secondary',
                        text: '{{ 'back'|trans }}'
                    },
                    {
                        action() { return this.next() },
                        text: '{{ 'next'|trans }}'
                    }
                ]
            });
            tour.addStep({
                title: '{{ 'tourStepWebsitesLocalesHeadline'|trans }}',
                text: '{{ 'tourStepWebsitesLocalesBody'|trans|raw }}',
                attachTo: { element: '.actions-section', on: 'bottom' },
                popperOptions: {
                    modifiers: [{name: 'offset', options: {offset: [0, 12]}}],
                },
                buttons: [
                    {
                        action() { return this.back() },
                        classes: 'shepherd-button-secondary',
                        text: '{{ 'back'|trans }}'
                    },
                    {
                        action() { return this.next() },
                        text: '{{ 'next'|trans }}'
                    }
                ]
            });
            tour.addStep({
                title: '{{ 'tourStepFoundBugHeadline'|trans }}',
                text: '{{ 'tourStepFoundBugBody'|trans }}',
                attachTo: { element: '.found-bug-footer-button', on: 'top' },
                popperOptions: {
                    modifiers: [{name: 'offset', options: {offset: [0, 15]}}],
                },
                buttons: [
                    {
                        action() { return this.back() },
                        classes: 'shepherd-button-secondary',
                        text: '{{ 'back' |trans }}'
                    },
                    {
                        action() { return this.next() },
                        text: '{{ 'next'|trans }}'
                    }
                ]
            });
            tour.addStep({
                title: '{{ 'tourStepFinishHeadline'|trans }}',
                text: '{{ 'tourStepFinishBody'|trans }}',
                attachTo: { element: null, on: 'center' },
                buttons: [
                    {
                        action() { return this.complete() },
                        text: '{{ 'finish'|trans }}'
                    }
                ]
            });

            tour.start();
        });
    </script>
    <style>
        body .shepherd-has-title .shepherd-content .shepherd-header {
            padding: .7em 1.35em;
        }
        body .shepherd-title {
            font-size: 16px;
        }
        body .shepherd-text {
            line-height: 1.5em;
            padding: 1.35em;
        }
    </style>
{% endblock %}
