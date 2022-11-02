<aside>
    <div class="sidebar-container">
        <div class="noselect" data-simplebar>
            <a class="cms-logo" href="{{ path('backend.homepage') }}">
                <img class="logo-image" src="{{ asset('/assets/core/backend/theme/images/logo-reverse.svg') }}" alt="Tulia CMS" />
            </a>
            <div class="user-area">
                {% set user = user() %}
                <a href="{{ path('backend.me') }}" class="user-avatar" title="{{ 'myAccountUsername'|trans({ username: user.name }) }}">
                    {% if user.avatar is defined and user.avatar %}
                        <img src="{{ asset(user.avatar) }}" />
                    {% else %}
                        <span class="text-avatar">{{ user_initials() }}</span>
                    {% endif %}
                    <div class="user-details">
                        {% if user.name is defined and user.name %}
                            <div class="user-name">{{ user.name }}</div>
                            <div class="user-email">{{ user.email }}</div>
                        {% else %}
                            <div class="user-name">{{ user.email }}</div>
                        {% endif %}
                    </div>
                </a>
            </div>
            <div class="lead-menu">
                {{ backend_menu(current_website().id, current_website().locale.code) }}
            </div>
        </div>
    </div>
    <div class="sidebar-footer">
        <a href="https://tuliacms.org/goto/slack" class="text-white" target="_blank" title="Tulia CMS on Slack"><i class="fab fa-slack"></i></a>
        <a href="https://tuliacms.org/goto/facebook" class="text-white" target="_blank" title="Tulia CMS on Facebook"><i class="fab fa-facebook"></i></a>
        <a href="https://tuliacms.org" class="text-white" target="_blank" title="Tulia CMS Website"><i class="fas fa-globe"></i></a>
        <a href="#" class="text-white found-bug-footer-button" target="_blank" title="{{ 'didYouFoundBug'|trans }}" data-bs-toggle="modal" data-bs-target="#tuliacms-found-bug-modal"><i class="fas fa-bug"></i> {{ 'didYouFoundBug'|trans }}</a>
    </div>
</aside>
