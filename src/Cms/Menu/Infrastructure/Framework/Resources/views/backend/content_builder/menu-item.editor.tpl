{% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}
{% trans_default_domain 'menu' %}

{{ form_render.form_begin(form) }}
{% set itemDetailsForm = context.itemDetailsForm %}

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-asterisk"></i> {{ 'basics'|trans }}
            </div>
            <div class="card-body">
                {{ form_row(itemDetailsForm.name) }}
                {{ form_row(itemDetailsForm.visibility) }}
                {{ form_row(itemDetailsForm.type) }}
                {{ form_row(itemDetailsForm.parent) }}
                {% for group in contentType.fieldGroups %}
                    {% if group.section == 'basics' %}
                        {% for field in group.fields %}
                            {{ form_render.form_row(form, field.code, contentType) }}
                        {% endfor %}
                    {% endif %}
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-network-wired"></i> {{ 'destination'|trans }}
            </div>
            <div class="card-body">
                {% set item = context.item %}
                {% include relative(_self, '../item/parts/type-homepage.tpl') %}
                {% include relative(_self, '../item/parts/type-url.tpl') %}
                {% for type in context.types %}
                    {% if item.type == type.type.type %}
                        <div class="menu-item-type" data-type="{{ type.type.type }}">
                            {{ type.selector.render(type.type, item.identity, websiteId, locale)|raw }}
                        </div>
                    {% else %}
                        <div class="menu-item-type d-none" data-type="{{ type.type.type }}">
                            {{ type.selector.render(type.type, null, websiteId, locale)|raw }}
                        </div>
                    {% endif %}
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-border-style"></i> {{ 'options'|trans }}
            </div>
            <div class="card-body">
                {{ form_row(itemDetailsForm.hash) }}
                {{ form_row(itemDetailsForm.identity) }}
                {{ form_row(itemDetailsForm.target) }}
                {% for group in contentType.fieldGroups %}
                    {% if group.section == 'options' %}
                        {% for field in group.fields %}
                            {{ form_render.form_row(form, field.code, contentType) }}
                        {% endfor %}
                    {% endif %}
                {% endfor %}
            </div>
        </div>
    </div>
</div>

<script nonce="{{ csp_nonce() }}">
    $(function () {
        $('#menu_item_details_form_type').change(function () {
            let container = $('.menu-item-type')
                .addClass('d-none')
                .filter('[data-type="' + $(this).val() + '"]')
                .removeClass('d-none');

            let identity = container.find('[data-identity="' + $(this).val() + '"]').val();

            updateIdentityField($(this).val(), identity);

            setTimeout(function () {
                container
                    .find('.item-type-field-autofocus')
                    .focus()
                ;
            }, 100);
        });

        $('[data-identity]').on('change keyup keydown blur', function () {
            updateIdentityField($(this).attr('data-identity'), $(this).val());
        });

        $('.menu-item-type[data-type="{{ item.type ?? app.request.get('content_builder_form_menu_item').type ?? 'simple:homepage' }}"]').removeClass('d-none');
    });

    let updateIdentityField = function (type, identity) {
        let currentType = $('#menu_item_details_form_type').val();

        if (currentType !== type) {
            return;
        }

        $('#menu_item_details_form_identity').val(identity);
    };
</script>

{{ form_render.form_end(form) }}
