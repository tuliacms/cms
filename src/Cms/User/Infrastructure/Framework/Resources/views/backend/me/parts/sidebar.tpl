<div class="sidebar">
    {% if user.avatar %}
        <div class="avatar" style="background-image:url('{{ asset(user.avatar) }}');"></div>
    {% endif %}
    <div class="username">{{ user.name ?? user.email }}</div>
</div>
