<a href="{{ path('backend.menu.item.edit', { menuId: row.menu_id, id: row.id }) }}" title="{{ row.name }}" class="link-title">
    <span class="boxur-depth boxur-depth-{{ row.level - 1 }}">
        {% if row.translated is defined and row.translated != '1' %}
            <span class="badge badge-info" data-bs-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
        {% endif %}
        {{ row.name }}
    </span>
</a>
