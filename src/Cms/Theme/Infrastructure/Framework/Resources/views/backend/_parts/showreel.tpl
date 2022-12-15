{% macro showreel(name, filepath) %}
    <div class="theme-image scroll-image-on-hover">
        <img src="{{ path('backend.theme.internal_image', { theme: name, filepath: filepath }) }}" alt="{{ name }} theme thumbnail">
    </div>
{% endmacro %}

{% macro javascript() %}
    <style>
        .theme-image {
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(0,0,0,.176);
        }
        .theme-image:before {
            content: "";
            display: block;
            padding-bottom: 55.5%;
            background-color: rgba(0,0,0,.3);
        }
        .theme-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
    </style>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('.scroll-image-on-hover').hover(function () {
                const image = $(this).find('img');
                const imageHeight = parseInt(image.css('height'));
                const viewHeight = parseInt($(this).css('height'));

                image.stop().animate({
                    top: - (imageHeight - viewHeight)
                }, (imageHeight / 200) * 1000, 'linear');
            }, function () {
                $(this).find('img').stop().animate({top: 0}, 200);
            });
        });
    </script>
{% endmacro %}
