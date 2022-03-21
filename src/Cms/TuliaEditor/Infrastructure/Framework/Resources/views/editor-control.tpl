{% assets ['tulia_editor', 'tulia_editor_new'] %}

<div id="{{ params.id }}-qaaaa"></div>

<script>
    $(function () {
        let structureSelector = '.tulia-editor-structure-field[data-tulia-editor-group-id="{{ params.group_id }}"]';
        let contentSelector = '.tulia-editor-content-field[data-tulia-editor-group-id="{{ params.group_id }}"]';

        let structure = $(structureSelector).val();

        if (!structure) {
            structure = {};
        } else {
            structure = JSON.parse(structure);
        }

        new TuliaEditorAdmin('#{{ params.id }}-qaaaa', {
            sink: {
                structure: structureSelector,
                content: contentSelector
            },
            structure: {
                source: structure
            },
            editor: {
                view: '{{ path('backend.tulia_editor.editor_view') }}',
            },
            start_point: 'editor'
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
            data: $('.tulia-editor-structure-field[data-tulia-editor-group-id="{{ params.group_id }}"]'),
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
