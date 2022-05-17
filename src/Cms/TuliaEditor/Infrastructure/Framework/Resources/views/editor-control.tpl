{% assets ['tulia_editor'] %}

<div id="{{ params.id }}"></div>

<script>
    $(function () {
        TuliaEditor.trans('{{ user().locale }}', 'default', {
            save: '{{ 'save'|trans }}',
            cancel: '{{ 'cancel'|trans }}',
            section: '{{ 'section'|trans({}, 'tulia-editor') }}',
            column: '{{ 'column'|trans({}, 'tulia-editor') }}',
            row: '{{ 'row'|trans({}, 'tulia-editor') }}',
            block: '{{ 'block'|trans({}, 'tulia-editor') }}',
            selectEditableElementToShowOptions: '{{ 'selectEditableElementToShowOptions'|trans({}, 'tulia-editor') }}',
            startTypingPlaceholder: '{{ 'startTypingPlaceholder'|trans({}, 'tulia-editor') }}',
            choseImage: '{{ 'choseImage'|trans({}, 'tulia-editor') }}',
            removeItem: '{{ 'removeItem'|trans({}, 'tulia-editor') }}',
            addItem: '{{ 'addItem'|trans({}, 'tulia-editor') }}',
            selectIcon: '{{ 'selectIcon'|trans({}, 'tulia-editor') }}',
            searchForIcon: '{{ 'searchForIcon'|trans({}, 'tulia-editor') }}',
            emptySearchResults: '{{ 'emptySearchResults'|trans({}, 'tulia-editor') }}',
            selectParentElement: '{{ 'selectParentElement'|trans({}, 'tulia-editor') }}',
            delete: '{{ 'delete'|trans({}, 'tulia-editor') }}',
            moveBackward: '{{ 'moveBackward'|trans({}, 'tulia-editor') }}',
            moveForward: '{{ 'moveForward'|trans({}, 'tulia-editor') }}',
            moveUp: '{{ 'moveUp'|trans({}, 'tulia-editor') }}',
            moveDown: '{{ 'moveDown'|trans({}, 'tulia-editor') }}',
            edit: '{{ 'edit'|trans({}, 'tulia-editor') }}',
            contentPreview: '{{ 'contentPreview'|trans({}, 'tulia-editor') }}',
            newSection: '{{ 'newSection'|trans({}, 'tulia-editor') }}',
            newBlock: '{{ 'newBlock'|trans({}, 'tulia-editor') }}',
            selected: '{{ 'selected'|trans({}, 'tulia-editor') }}',
            structure: '{{ 'structure'|trans({}, 'tulia-editor') }}',
            startCreatingNewContent: '{{ 'startCreatingNewContent'|trans({}, 'tulia-editor') }}',
            emptySection: '{{ 'emptySection'|trans({}, 'tulia-editor') }}',
            emptyRow: '{{ 'emptyRow'|trans({}, 'tulia-editor') }}',
            emptyColumn: '{{ 'emptyColumn'|trans({}, 'tulia-editor') }}',
        });

        let structureSelector = '.tulia-editor-structure-field[data-tulia-editor-group-id="{{ params.group_id }}"]';
        let contentSelector = '.tulia-editor-content-field[data-tulia-editor-group-id="{{ params.group_id }}"]';

        let structure = $(structureSelector).val();

        if (!structure) {
            structure = {};
        } else {
            structure = JSON.parse(structure);
        }

        new TuliaEditor.Editor('#{{ params.id }}', {
            sink: {
                structure: structureSelector,
                content: contentSelector
            },
            structure: {
                source: structure,
                preview: $(contentSelector).val(),
            },
            editor: {
                view: '{{ path('backend.tulia_editor.editor_view') }}',
                preview: '{{ path('backend.tulia_editor.editor_preview') }}',
            },
            /*blocks: {
                "core-imageblock": {
                    image_resolve_path: '{{ path('filemanager.resolve.image.size', { size: '{size}', id: '{id}', filename: '{filename}' }) }}',
                    filemanager_endpoint: '{{ path('backend.filemanager.endpoint') }}',
                },
            },*/
            filemanager: {
                image_resolve_path: '{{ path('filemanager.resolve.image.size', { size: '{size}', id: '{id}', filename: '{filename}' }) }}',
                endpoint: '{{ path('backend.filemanager.endpoint') }}',
            },
            //start_point: 'editor',
            locale: '{{ user().locale }}'
        });
    });
</script>
