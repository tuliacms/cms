{% embed '@backend/content_builder/layout/_parts/editor/form_layout.sidebar.tpl' %}
    {% set widgetDetailsForm = context.widgetDetailsForm.createView %}

    {% block page_header %}
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        {{ form_row(widgetDetailsForm.name, { attr: { autofocus: 'true' } }) }}
                    </div>
                    <div class="col-6">
                        {{ form_row(widgetDetailsForm.space) }}
                    </div>
                </div>
            </div>
        </div>
    {% endblock %}

    {% block sidebar_accordion %}
        <div class="accordion-section">
            <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-widget-look">
                {{ 'widgetDetails'|trans({}, 'widgets') }}
            </div>
            <div id="form-collapse-widget-look" class="accordion-collapse collapse show">
                <div class="accordion-section-body">
                    {{ form_row(widgetDetailsForm.visibility) }}
                    {{ form_row(widgetDetailsForm.title) }}
                    {{ form_row(widgetDetailsForm.styles) }}
                    {{ form_row(widgetDetailsForm.htmlClass) }}
                    {{ form_row(widgetDetailsForm.htmlId) }}
                </div>
            </div>
        </div>
    {% endblock %}
{% endembed %}
