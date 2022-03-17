{% assets ['tulia_editor', 'tulia_editor_new'] %}


<div id="tued-editor"{# id="{{ params.id }}" #}></div>

<script>
    $(function () {
        new TuliaEditorAdmin('#tued-editor', {
            vue_path: 'https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js',
            data: $('.tulia-editor-payload-field[data-tulia-editor-group-id="{{ params.group_id }}"]'),
            layout: {
                viewMode: 'editor'
            }/*,
            assets: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css'
                ],
                scripts: [
                    'https://code.jquery.com/jquery-3.5.1.min.js',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js'
                ]
            }*/
        });
    });
</script>

























{#{% assets theme.config.all('tulia_editor_plugin')|keys %}#}

{#{% set data = entity.attribute('tulia-editor-data', 'null') %}#}

<textarea style="display: none !important;" id="tulia-editor-{{ params.id }}-content" name="{{ name }}">{{ content|raw }}</textarea>

{#{% if content and (data is empty or data == 'null') %}
    <div class="alert alert-info alert-dismissible fade show">
        <p>{{ 'contentNotSupportedByEditorCopyToNotLoss'|trans({}, 'tulia-editor') }}</p>
        <textarea class="form-control">{{ content|raw }}</textarea>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
{% endif %}#}

<div id="{{ params.id }}"></div>

<script nonce="{{ csp_nonce() }}">
    $(function () {
        TuliaEditor.create('#{{ params.id }}', {
            data: $('.tulia-editor-payload-field[data-tulia-editor-group-id="{{ params.group_id }}"]'),
            framework: 'bootstrap-5',
            lang: '{{ user().locale }}',
            {#include: {
                stylesheets: {{ (assetter_standalone_assets(theme.config.all('tulia_editor_asset')|keys).stylesheets)|json_encode|raw }}
            },#}
            styles: {
                predefined: {
                    heading: {
                        "heading-primary": "style.predefined.heading.primary"
                    }
                }
            },
            setup: function (editor) {
                editor.on('save', function (content) {
                    $('#tulia-editor-{{ params.id }}-content').val(content.content);
                    $('#node_form_tulia_editor_data').val(JSON.stringify(content));
                });
            }
        });

        TuliaEditor.i18n['pl']['style.predefined.heading.primary'] = 'Styl 1';
        TuliaEditor.i18n['en']['style.predefined.heading.primary'] = 'Style 1';
    });
</script>
