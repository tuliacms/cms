{% assets ['content_builder.layout_builder.routable_content_type'] %}

{% trans_default_domain 'content_builder' %}

<div id="content-builder-content-block-builder"></div>

<script nonce="{{ csp_nonce() }}">
    window.ContentBuilderLayoutBuilder = {
        translations: {
            pageTitle: '{{ pageTitle|raw }}',
            yes: '{{ 'yes'|trans({}, 'messages') }}',
            no: '{{ 'no'|trans({}, 'messages') }}',
            close: '{{ 'close'|trans({}, 'messages') }}',
            cancel: '{{ 'cancel'|trans({}, 'messages') }}',
            create: '{{ 'create'|trans({}, 'messages') }}',
            icon: '{{ 'icon'|trans({}, 'messages') }}',
            save: '{{ 'save'|trans({}, 'messages') }}',
            contentTypeName: '{{ 'contentTypeName'|trans }}',
            contentTypeNameInfo: '{{ 'contentTypeNameInfo'|trans }}',
            contentTypeCode: '{{ 'contentTypeCode'|trans }}',
            contentTypeCodeHelp: '{{ 'contentTypeCodeHelp'|trans }}',
            editContentTypeDetails: '{{ 'editContentTypeDetails'|trans }}',
            addNewSection: '{{ 'addNewSection'|trans }}',
            addNewField: '{{ 'addNewField'|trans }}',
            fieldLabel: '{{ 'fieldLabel'|trans }}',
            fieldLabelHelp: '{{ 'fieldLabelHelp'|trans }}',
            fieldCode: '{{ 'fieldCode'|trans }}',
            fieldCodeHelp: '{{ 'fieldCodeHelp'|trans }}',
            youCannotCreateTwoFieldsWithTheSameCode: '{{ 'youCannotCreateTwoFieldsWithTheSameCode'|trans }}',
            theseOptionsWillNotBeEditableAfterSave: '{{ 'theseOptionsWillNotBeEditableAfterSave'|trans }}',
            editField: '{{ 'editField'|trans }}',
            fieldType: '{{ 'fieldType'|trans }}',
            removeField: '{{ 'removeField'|trans }}',
            removeSection: '{{ 'removeSection'|trans }}',
            multilingualField: '{{ 'multilingualField'|trans }}',
            multilingualFieldInfo: '{{ 'multilingualFieldInfo'|trans }}',
            nextStep: '{{ 'nextStep'|trans }}',
            pleaseFillThisField: '{{ 'pleaseFillThisField'|trans }}',
            fieldCodeMustContainOnlyAlphanumsAndUnderline: '{{ 'fieldCodeMustContainOnlyAlphanumsAndUnderline'|trans }}',
            fieldTypeConfiguration: '{{ 'fieldTypeConfiguration'|trans }}',
            fieldDetails: '{{ 'fieldDetails'|trans }}',
            fieldTypeConstraints: '{{ 'fieldTypeConstraints'|trans }}',
            routableType: '{{ 'routableType'|trans }}',
            routableTypeHelp: '{{ 'routableTypeHelp'|trans }}',
            hierarchicalType: '{{ 'hierarchicalType'|trans }}',
            hierarchicalTypeHelp: '{{ 'hierarchicalTypeHelp'|trans }}',
            pleaseSelectValue: '{{ 'pleaseSelectValue'|trans }}',
            fieldHasErrors: '{{ 'fieldHasErrors'|trans }}',
            routingStrategy: '{{ 'routingStrategy'|trans }}',
            routingStrategyHelp: '{{ 'routingStrategyHelp'|trans }}',
            themeRequiresContentFieldToBeExistence: '{{ 'themeRequiresContentFieldToBeExistence'|trans|raw }}',
            toggleActiveness: '{{ 'toggleActiveness'|trans }}',
        },
        fieldTypes: {{ fieldTypes|json_encode|raw }},
        routingStrategies: {{ routingStrategies|json_encode|raw }},
        model: {{ model|json_encode|raw }},
        errors: {{ errors|json_encode|raw }},
        multilingual: {{ multilingual ? 'true' : 'false' }},
        creationMode: {{ creationMode ? 'true' : 'false' }},
        listingUrl: '{{ path('backend.content.type.homepage') }}',
        csrfToken: '{{ csrf_token('create-content-type') }}',
        themeNodeContentFieldName: '{{ theme.config.nodeContentField }}'
    };
</script>
