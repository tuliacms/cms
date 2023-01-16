<a href="{{ path('backend.node.edit', { node_type: row.type, id: row.id }) }}" class="link-title" title="{{ row.title }}">
    {% set attributes = row.attributes.find() %}
    <span class="boxur-depth boxur-depth-{{ row.level }}">
        {% if attributes.thumbnail is defined %}
            <img src="{{ image_url(attributes.thumbnail.__toString, { size: 'thumbnail' }) }}" style="height:31px;float:left" class="me-2 d-inline-block" alt="" />
        {% endif %}
        {% if row.translated is defined and row.translated != '1' %}
            <span class="badge badge-info" data-bs-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
        {% endif %}
        {% if row.status == 'draft' %}
            <span class="badge badge-secondary"><i class="dropdown-icon fas fa-pen-alt"></i> &nbsp;{{ 'draft'|trans }}</span>
        {% elseif row.status == 'trashed' %}
            <span class="badge badge-warning"><i class="dropdown-icon fas fa-trash"></i> &nbsp;{{ 'trashed'|trans }}</span>
        {% endif %}
        <span class="node-title">{{ row.title }}</span>
        <br />
        <span class="slug">{{ 'slugValue'|trans({ slug: row.slug }) }}</span>
        {% if row.purposes is not empty %}
            <br />
            {% for purpose in row.purposes %}
                {% set porposeLabel = trans_exists('purposeType.' ~ purpose, {}, 'node')
                    ? ('purposeType.' ~ purpose)|trans({}, 'node')
                    : purpose %}
                <span class="badge badge-secondary">
                    {{ 'purposeWithName'|trans({ purpose: porposeLabel }, 'node') }}
                </span>
            {% endfor %}
        {% endif %}
    </span>
</a>
