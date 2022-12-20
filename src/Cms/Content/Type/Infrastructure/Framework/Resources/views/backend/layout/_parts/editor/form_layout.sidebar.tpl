{% macro tab(id, group, form) %}
    {% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}

    <li class="nav-item">
        <a
            href="#"
            class="nav-link {{ (group.active ?? false) ? 'active' : '' }}"
            data-bs-toggle="tab"
            data-bs-target="#tab-{{ id }}"
        >
            {{ group.name|trans({}, 'content_builder') }}
            {{ badge.errors_count(form, group.fieldsCodes|default([])) }}
        </a>
    </li>
{% endmacro %}

{% macro tab_content(id, active, group, form, contentType) %}
    {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

    <div class="tab-pane fade {{ active ? 'show active' : '' }}" id="tab-{{ id }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {% for field in group.fields %}
                        {{ form_render.form_row(form, field.code, contentType) }}
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro tab_rest_content(id, form) %}
    <div class="tab-pane fade" id="tab-{{ id }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="empty-form-section-placeholder" data-placeholder="{{ 'thereAreNoOtherSettings'|trans }}">{{ form_rest(form) }}</div>
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro section(id, group, form, translationDomain, contentType) %}
    {% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
    {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

    <div class="accordion-section">
        <div
            class="accordion-section-button{{ (group.active ?? false) ? '' : ' collapsed' }}"
            data-bs-toggle="collapse"
            data-bs-target="#form-collapse-sidebar-{{ id }}"
        >
            {{ group.name|trans({}, translationDomain) }}
            {{ badge.errors_count(form, group.fieldsCodes|default([])) }}
        </div>
        <div
            id="form-collapse-sidebar-{{ id }}"
            class="accordion-collapse collapse{{ (group.active ?? false) ? ' show' : '' }}"
        >
            <div class="accordion-section-body">
                {{ form_render.render_fields(form, group.fields, contentType) }}
            </div>
        </div>
    </div>
{% endmacro %}

{% if context.partial_view is defined and context.partial_view is not empty %}
    {% import context.partial_view as partial_view %}
    {% set hasPartialView = true %}
{% else %}
    {% set hasPartialView = false %}
{% endif %}

{% block layout %}
    {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}
    {% set contentType = attributesForm.vars.content_type %}

    {{ form_render.form_begin(form) }}

    <div class="page-form"> {# id="node-form" #}
        <div class="page-form-sidebar">
            <div class="accordion">
                {% if hasPartialView and partial_view.sidebar_accordion is defined %}
                    {{ partial_view.sidebar_accordion(form, context) }}
                {% endif %}
                {% for group in contentType.fieldGroups %}
                    {% if group.section == 'sidebar' %}
                        {{ _self.section(group.code, group, attributesForm, null, contentType) }}
                    {% endif %}
                {% endfor %}
            </div>
        </div>
        <div class="page-form-content">
            {% if hasPartialView and partial_view.page_header is defined %}
                {{ partial_view.page_header(form, context) }}
            {% endif %}
            <ul class="nav nav-tabs page-form-tabs" role="tablist">
                {% if hasPartialView and partial_view.page_tabs is defined %}
                    {% set pageTabsBlock = partial_view.page_tabs(form, context) %}
                {% else %}
                    {% set pageTabsBlock = null %}
                {% endif %}

                {{ pageTabsBlock|raw }}
                {% set loopIndex = 0 %}
                {% for group in contentType.fieldGroups %}
                    {% if group.section == 'main' %}
                        {{ _self.tab(group.code, {
                            active: pageTabsBlock is empty and loopIndex == 0,
                            name: group.name|trans,
                            fields: group.fields
                        }, attributesForm) }}
                        {% set loopIndex = loopIndex + 1 %}
                    {% endif %}
                {% endfor %}

                {{ _self.tab('rest', {
                    active: false,
                    name: 'otherSettings'|trans({}, 'messages'),
                    fields: []
                }, attributesForm) }}
            </ul>
            <div class="tab-content">
                {% if hasPartialView and partial_view.page_tabs_content is defined %}
                    {% set pageTabsContentBlock = partial_view.page_tabs_content(form, context) %}
                {% else %}
                    {% set pageTabsContentBlock = null %}
                {% endif %}

                {{ pageTabsContentBlock|raw }}
                {% set loopIndex = 0 %}
                {% for group in contentType.fieldGroups %}
                    {% if group.section == 'main' %}
                        {{ _self.tab_content(
                            group.code,
                            pageTabsContentBlock is empty and loopIndex == 0,
                            group,
                            attributesForm,
                            contentType
                        ) }}
                        {% set loopIndex = loopIndex + 1 %}
                    {% endif %}
                {% endfor %}

                {{ _self.tab_rest_content('rest', attributesForm) }}
            </div>
        </div>
    </div>

    {{ form_render.form_end(form) }}

    {% if hasPartialView and partial_view.javascripts is defined %}
        {{ partial_view.javascripts(form, context) }}
    {% endif %}
{% endblock %}
