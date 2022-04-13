{% assets ['tulia_editor'] %}

<div id="{{ params.id }}-qaaaa"></div>

<script>
    $(function () {
        window.TuliaEditor.TuliaEditor.translations['{{ user().locale }}'] = {
            save: '{{ 'save'|trans }}',
            cancel: '{{ 'cancel'|trans }}',
            section: '{{ 'section'|trans({}, 'tulia-editor') }}',
            column: '{{ 'column'|trans({}, 'tulia-editor') }}',
            row: '{{ 'row'|trans({}, 'tulia-editor') }}',
            block: '{{ 'block'|trans({}, 'tulia-editor') }}',
        };

        let structureSelector = '.tulia-editor-structure-field[data-tulia-editor-group-id="{{ params.group_id }}"]';
        let contentSelector = '.tulia-editor-content-field[data-tulia-editor-group-id="{{ params.group_id }}"]';

        let structure = $(structureSelector).val();

        if (!structure) {
            structure = {};
        } else {
            structure = JSON.parse(structure);
        }

        new window.TuliaEditor.TuliaEditor('#{{ params.id }}-qaaaa', {
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
            //lang: '{{ user().locale }}',
            {#include: {
                stylesheets: {{ (assetter_standalone_assets(theme.config.all('tulia_editor_asset')|keys).stylesheets)|json_encode|raw }}
            },#}
            start_point: 'editor',
            locale: '{{ user().locale }}'
        });
    });
</script>
