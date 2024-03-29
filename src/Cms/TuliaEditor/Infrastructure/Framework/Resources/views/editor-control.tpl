{% assets ['tulia_editor'] %}

{% set themes = [theme().name] %}

{% if theme().hasParent %}
    {% set themes = themes|merge([theme().parentName]) %}
{% endif %}

{% if 'Tulia/DefaultTheme' in themes %}
    <div class="alert alert-warning">
        <strong>Tulia Editor:</strong> {{ 'noActiveThemeDetectedCannotEditContent'|trans({link: '<a href="' ~ path('backend.theme') ~ '" style="font-weight: bold;">' ~ 'themes'|trans ~ '</a>'}, 'tulia-editor')|raw }}
    </div>
{% else %}
    <div id="{{ params.id }}"></div>
{% endif %}

<script>
    $(function () {
        TuliaEditor.trans('{{ user().locale }}', 'default', {
            save: '{{ 'save'|trans }}',
            cancel: '{{ 'cancel'|trans }}',
            yes: '{{ 'yes'|trans }}',
            no: '{{ 'no'|trans }}',
            bottom: '{{ 'bottom'|trans }}',
            left: '{{ 'left'|trans }}',
            right: '{{ 'right'|trans }}',
            top: '{{ 'top'|trans }}',
            selectSomeOptions: '{{ 'selectOption'|trans }}',
            visibility: '{{ 'visibility'|trans }}',
            section: '{{ 'section'|trans({}, 'tulia-editor') }}',
            column: '{{ 'column'|trans({}, 'tulia-editor') }}',
            row: '{{ 'row'|trans({}, 'tulia-editor') }}',
            block: '{{ 'block'|trans({}, 'tulia-editor') }}',
            selectEditableElementToShowOptions: '{{ 'selectEditableElementToShowOptions'|trans({}, 'tulia-editor') }}',
            noEditOptionsForThisElement: '{{ 'noEditOptionsForThisElement'|trans({}, 'tulia-editor') }}',
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
            addBlock: '{{ 'addBlock'|trans({}, 'tulia-editor') }}',
            addSectionBelow: '{{ 'addSectionBelow'|trans({}, 'tulia-editor') }}',
            addRow: '{{ 'addRow'|trans({}, 'tulia-editor') }}',
            addColumn: '{{ 'addColumn'|trans({}, 'tulia-editor') }}',
            addColumnBefore: '{{ 'addColumnBefore'|trans({}, 'tulia-editor') }}',
            addColumnAfter: '{{ 'addColumnAfter'|trans({}, 'tulia-editor') }}',
            selectImage: '{{ 'selectImage'|trans({}, 'tulia-editor') }}',
            nextSlide: '{{ 'nextSlide'|trans({}, 'tulia-editor') }}',
            prevSlide: '{{ 'prevSlide'|trans({}, 'tulia-editor') }}',
            editMap: '{{ 'editMap'|trans({}, 'tulia-editor') }}',
            finishMapEditing: '{{ 'finishMapEditing'|trans({}, 'tulia-editor') }}',
            mapHeight: '{{ 'mapHeight'|trans({}, 'tulia-editor') }}',
            mapZoom: '{{ 'mapZoom'|trans({}, 'tulia-editor') }}',
            imageSize: '{{ 'imageSize'|trans({}, 'tulia-editor') }}',
            clearImage: '{{ 'clearImage'|trans({}, 'tulia-editor') }}',
            containerSize: '{{ 'containerSize'|trans({}, 'tulia-editor') }}',
            containerSizeDefaultWidth: '{{ 'containerSizeDefaultWidth'|trans({}, 'tulia-editor') }}',
            containerSizeFullWidth: '{{ 'containerSizeFullWidth'|trans({}, 'tulia-editor') }}',
            containerSizeFullWidthNoPadding: '{{ 'containerSizeFullWidthNoPadding'|trans({}, 'tulia-editor') }}',
            anchorId: '{{ 'anchorId'|trans({}, 'tulia-editor') }}',
            anchorIdHelp: '{{ 'anchorIdHelp'|trans({}, 'tulia-editor') }}',
            margin: '{{ 'margin'|trans({}, 'tulia-editor') }}',
            padding: '{{ 'padding'|trans({}, 'tulia-editor') }}',
            sizingDefault: '{{ 'sizingDefault'|trans({}, 'tulia-editor') }}',
            inheritValue: '{{ 'inheritValue'|trans({}, 'tulia-editor') }}',
            calculatedVisibilityVisible: '{{ 'calculatedVisibilityVisible'|trans({}, 'tulia-editor') }}',
            calculatedVisibilityInvisible: '{{ 'calculatedVisibilityInvisible'|trans({}, 'tulia-editor') }}',
            youtubeVideoUrl: '{{ 'youtubeVideoUrl'|trans({}, 'tulia-editor') }}',
            aspectRatio: '{{ 'aspectRatio'|trans({}, 'tulia-editor') }}',
            columnsNumber: '{{ 'columnsNumber'|trans({}, 'tulia-editor') }}',
            imagesBottomMargin: '{{ 'imagesBottomMargin'|trans({}, 'tulia-editor') }}',
            onclickGallery: '{{ 'onclickGallery'|trans({}, 'tulia-editor') }}',
        });

        let structureSelector = '.tulia-editor-structure-field[data-tulia-editor-group-id="{{ params.group_id }}"]';
        let contentSelector = '.tulia-editor-content-field[data-tulia-editor-group-id="{{ params.group_id }}"]';

        new TuliaEditor.Admin('#{{ params.id }}', {
            sink: {
                structure: structureSelector,
                content: contentSelector
            },
            editor: {
                view: '{{ path('backend.tulia_editor.editor_view') }}',
                preview: '{{ path('backend.tulia_editor.editor_preview') }}',
            },
            /*blocks: {
                "core-imageblock": {
                    image_resolve_path: '{{ path('frontend.filemanager.resolve.image.size', { size: '{size}', id: '{id}', filename: '{filename}' }) }}',
                    filemanager_endpoint: '{{ path('backend.filemanager.endpoint') }}',
                },
            },*/
            filemanager: {
                image_resolve_path: '{{ path('frontend.filemanager.resolve.image.size', { size: '{size}', id: '{id}', filename: '{filename}' }) }}',
                endpoint: '{{ path('backend.filemanager.endpoint') }}',
                image_sizes: {{ images_sizes()|json_encode|raw }},
            },
            //start_point: 'editor',
            locale: '{{ user().locale }}',
            cms_integration: {
                endpoints: {
                    form_list: '{{ path('backend.contact_form.list_forms') }}',
                    taxonomy_term_list: '{{ path('backend.term.list_terms_of_taxonomies') }}',
                }
            },
            themes: ['{{ themes|join("', '")|raw }}'],
            css_framework: '{{ theme().config.cssFramework }}',
            debug: {{ is_dev_env() ? 'true' : 'false' }},
        });
    });
</script>
