{% macro page_header(form, context) %}
    <div class="page-form-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {{ form_row(form.title) }}
                </div>
                {% if form.slug is defined %}
                    <div class="col">
                        {{ form_row(form.slug) }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endmacro %}

{% macro sidebar_accordion(form, context) %}
    <div class="accordion-section">
        <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-status">
            {{ 'publicationStatus'|trans }}
        </div>
        <div id="form-collapse-sidebar-status" class="accordion-collapse collapse show">
            <div class="accordion-section-body">
                {{ form_row(form.published_at) }}

                {% set publishedToManually = form.published_to.vars.value != '' %}
                <div class="node-published-to-selector mb-4">
                    <div class="published-to-date-selector{{ publishedToManually ? '' : ' d-none' }}">
                        {{ form_row(form.published_to) }}
                    </div>
                    <div class="published-to-checkbox">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="node-published-to-switch"{{ publishedToManually ? ' checked="checked"' : '' }} />
                            <label class="custom-control-label" for="node-published-to-switch">{{ 'setPublicationEndDate'|trans }}</label>
                        </div>
                    </div>
                </div>

                {{ form_row(form.status) }}
                {{ form_row(form.author_id) }}
                {{ form_row(form.purposes) }}
                {{ form_row(form.main_category) }}
                {{ form_row(form.additional_categories) }}
                {#{{ form_row(form.parent_id) }}#}
            </div>
        </div>
    </div>
{% endmacro %}

{% macro javascripts() %}
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            let show = function () {
                let d = new Date();

                $('.published-to-date-selector').removeClass('d-none');
                $('#node_details_form_published_to').val(
                    d.getFullYear() + '-' +
                    String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    String(d.getDate()).padStart(2, '0') + ' ' +
                    String(d.getHours()).padStart(2, '0') + ':' +
                    String(d.getMinutes()).padStart(2, '0') + ':' +
                    String(d.getSeconds()).padStart(2, '0')
                );
            };
            let hide = function () {
                $('.published-to-date-selector').addClass('d-none');
                $('#node_details_form_published_to').val('');
            };
            $('#node-published-to-switch').change(function () {
                if ($(this).is(':checked')) {
                    show();
                } else {
                    hide();
                }
            });
        });
    </script>
{% endmacro %}
