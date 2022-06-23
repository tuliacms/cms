{% embed '@backend/content_builder/layout/_parts/editor/form_layout.sidebar.tpl' %}
    {% block page_header %}
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        {{ form_row(context.nodeDetailsForm.title) }}
                    </div>
                    {% if context.nodeDetailsForm.slug is defined %}
                        <div class="col">
                            {{ form_row(context.nodeDetailsForm.slug) }}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    {% endblock %}

    {% block sidebar_accordion %}
        <div class="accordion-section">
            <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-status">
                {{ 'publicationStatus'|trans }}
            </div>
            <div id="form-collapse-sidebar-status" class="accordion-collapse collapse show">
                <div class="accordion-section-body">
                    {{ form_row(context.nodeDetailsForm.published_at) }}

                    {% set publishedToManually = context.nodeDetailsForm.published_to.vars.value != '' %}
                    <div class="node-published-to-selector mb-4">
                        <div class="published-to-date-selector{{ publishedToManually ? '' : ' d-none' }}">
                            {{ form_row(context.nodeDetailsForm.published_to) }}
                        </div>
                        <div class="published-to-checkbox">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="node-published-to-switch"{{ publishedToManually ? ' checked="checked"' : '' }} />
                                <label class="custom-control-label" for="node-published-to-switch">{{ 'setPublicationEndDate'|trans }}</label>
                            </div>
                        </div>
                    </div>

                    {{ form_row(context.nodeDetailsForm.status) }}
                    {{ form_row(context.nodeDetailsForm.author_id) }}
                    {{ form_row(context.nodeDetailsForm.purposes) }}
                    {{ form_row(context.nodeDetailsForm.parent_id) }}
                </div>
            </div>
        </div>
    {% endblock %}
{% endembed %}

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
