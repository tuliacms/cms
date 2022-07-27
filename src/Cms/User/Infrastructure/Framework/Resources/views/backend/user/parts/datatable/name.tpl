{% set name %}
    {% if row.name is defined and row.name is not empty %}
        {{ row.name }}
    {% else %}
        {{ row.email }}
    {% endif %}
{% endset %}

{% set user = user() %}

<a href="{{ path('backend.user.edit', { id: row.id }) }}" title="{{ name }}" class="link-title">
    {% if user.id == row.id %}
        <b>[{{ 'yourAccount'|trans({}, 'users') }}]</b>
    {% endif %}
    {{ name }}
    <br /><span class="slug">{{ 'emailAddress'|trans({ email: row.email }, 'users') }}</span>
</a>
