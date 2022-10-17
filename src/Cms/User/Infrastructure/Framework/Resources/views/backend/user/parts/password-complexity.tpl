{% trans_default_domain 'users' %}
{% set complexity = user_password_complexity() %}

<div class="card text-bg-light mb-3">
    <div class="card-header">{{'passwordComplexityRequirements'|trans }}</div>
    <div class="card-body">
        <ul class="mb-0">
            <li>{{ 'passwordMimimumLength'|trans({ 'num': complexity.minLength }) }}</li>
            {% if complexity.minDigits %}
                <li>{{ 'passwordMimimumDigits'|trans({ 'num': complexity.minDigits }) }}</li>
            {% endif %}
            {% if complexity.minSpecialChars %}
                <li>{{ 'passwordMimimumSpecialCharacters'|trans({ 'num': complexity.minSpecialChars }) }}</li>
            {% endif %}
            {% if complexity.minBigLetters %}
                <li>{{ 'passwordMimimumBigLetters'|trans({ 'num': complexity.minBigLetters }) }}</li>
            {% endif %}
            {% if complexity.minSmallLetters %}
                <li>{{ 'passwordMimimumSmallLetters'|trans({ 'num': complexity.minSmallLetters }) }}</li>
            {% endif %}
        </ul>
    </div>
</div>
