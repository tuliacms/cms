{% extends 'theme' %}

{% block content %}
    <div class="container-xxl">
        <div class="row">
            <div class="col">
                <form method="post">
                    {% if error %}
                        <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
                    {% endif %}

                    {% if app.user %}
                        <div class="mb-3">
                            You are logged in as {{ app.user.username }}, <a href="{{ path('frontend.logout') }}">Logout</a>
                        </div>
                    {% endif %}

                    <h1 class="mb-3">Please sign in</h1>
                    <label for="inputEmail">Email</label>
                    <input type="text" value="{{ last_username }}" name="_username" id="inputEmail" class="form-control" required autofocus>
                    <label for="inputPassword">Password</label>
                    <input type="password" name="_password" id="inputPassword" class="form-control" required>

                    <input type="hidden" name="_token" value="{{ csrf_token('authenticate') }}">

                    <div class="checkbox mb-3">
                        <label>
                            <input type="checkbox" name="_remember_me"> Remember me
                        </label>
                    </div>

                    <button class="btn btn-lg btn-primary" type="submit">
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </div>
{% endblock %}
