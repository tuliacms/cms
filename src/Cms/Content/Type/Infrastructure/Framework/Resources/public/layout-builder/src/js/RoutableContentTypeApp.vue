<template>
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a :href="options.listingUrl" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> Anuluj</a>
                <button class="btn btn-success btn-icon-left" type="button" @click="form.save()"><i class="btn-icon fas fa-save"></i> Zapisz</button>
            </div>
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ translations.pageTitle }}</h1>
        </div>
        <div class="pane-body p-0">
            <div class="page-form" id="node-form">
                <div class="page-form-sidebar">
                    <form method="POST" id="ctb-form" style="display:none">
                        <textarea name="node_type" id="ctb-form-field-node-type"></textarea>
                        <input type="text" name="_token" :value="options.csrfToken"/>
                    </form>
                    <div class="ctb-sections-container">
                        <div class="ctb-section ctb-section-internal-fields">
                            <div class="ctb-section-label">
                                {{ translations.internalFields }}
                            </div>
                            <div class="ctb-section-fields-container">
                                <div class="ctb-sortable-fields">
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.title }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.slug }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.publishedAt }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.publicationStatus }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.author }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.nodePurpose }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <SectionsList
                        :translations="translations"
                        :sections="model.layout.sidebar.sections"
                        :errors="ObjectUtils.get(view.errors, 'layout.sidebar.sections', [])"
                    ></SectionsList>
                </div>
                <div class="page-form-content">
                    <div class="page-form-header">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="ctb-node-type-name">{{ translations.contentTypeName }}</label>
                                        <input type="text" :class="{ 'form-control': true, 'is-invalid': view.form.type_validation.name.valid === false }" id="ctb-node-type-name" v-model="model.type.name" @keyup="generateTypeCode()" @change="form.validate()" />
                                        <div class="form-text">{{ translations.contentTypeNameInfo }}</div>
                                        <div v-if="view.form.type_validation.name.valid === false" class="invalid-feedback">{{ view.form.type_validation.name.message }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="ctb-node-type-code">{{ translations.contentTypeCode }}</label>
                                        <input type="text" :disabled="view.creation_mode !== true" :class="{ 'form-control': true, 'is-invalid': view.form.type_validation.code.valid === false }" id="ctb-node-type-code" v-model="model.type.code" @change="form.validate()" />
                                        <div class="form-text">{{ translations.contentTypeCodeHelp }}</div>
                                        <div v-if="view.form.type_validation.code.valid === false" class="invalid-feedback">{{ view.form.type_validation.code.message }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="ctb-form-type-icon" class="form-label">{{ translations.icon }}</label>
                                    <input type="email" class="form-control" id="ctb-form-type-icon" v-model="model.type.icon" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="ctb-form-type-hierarchical" class="form-label">{{ translations.hierarchicalType }}</label>
                                    <ChosenSelect id="ctb-form-type-hierarchical" v-model="model.type.isHierarchical">
                                        <option value="1">{{ translations.yes }}</option>
                                        <option value="0">{{ translations.no }}</option>
                                    </ChosenSelect>
                                    <div class="form-text">{{ translations.hierarchicalTypeHelp }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="ctb-form-type-routable" class="form-label">{{ translations.routableType }}</label>
                                    <ChosenSelect id="ctb-form-type-routable" v-model="model.type.isRoutable">
                                        <option value="1">{{ translations.yes }}</option>
                                        <option value="0">{{ translations.no }}</option>
                                    </ChosenSelect>
                                    <div class="form-text">{{ translations.routableTypeHelp }}</div>
                                </div>
                                <div class="col-6 mb-3" v-if="model.type.isRoutable === '1'">
                                    <label for="ctb-form-type-routing-strategy" class="form-label">{{ translations.routingStrategy }}</label>
                                    <ChosenSelect id="ctb-form-type-routing-strategy" v-model="model.type.routingStrategy">
                                        <option v-for="strategy in options.routingStrategies" :id="strategy.id" :value="strategy.id">{{ strategy.label }}</option>
                                    </ChosenSelect>
                                    <div class="form-text">{{ translations.routingStrategyHelp }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content ctb-section-main-tabs-contents">
                        <SectionsList
                            :sections="model.layout.main.sections"
                            :errors="ObjectUtils.get(view.errors, 'layout.main.sections', [])"
                        ></SectionsList>
                    </div>
                </div>
            </div>
            <FieldCreator
                :fieldTypes="options.fieldTypes"
                :showMultilingualOption="true"
            ></FieldCreator>
            <FieldEditor
                :field="view.form.field_editor"
                :fieldTypes="options.fieldTypes"
                :showMultilingualOption="true"
            ></FieldEditor>
        </div>
    </div>
</template>

<script setup>
const { defineProps, provide, onMounted, reactive } = require('vue');
const FieldCreator = require('./components/FieldCreator.vue').default;
const FieldEditor = require('./components/FieldEditor.vue').default;
const SectionsList = require('./components/SectionsList.vue').default;
const ChosenSelect = require('./components/ChosenSelect.vue').default;
const ObjectUtils = require('./shared/ObjectUtils.js').default;

const errors = window.ContentBuilderLayoutBuilder.errors;
const view = reactive({
    modal: {
        field_creator: null,
        field_editor: null,
    },
    errors: errors,
    form: {
        code_field_changed: false,
        field_creator_section_code: null,
        field_creator_parent_field: null,
        field_editor: {
            code: {value: null, valid: true, message: null},
            type: {value: null, valid: true, message: null},
            name: {value: null, valid: true, message: null},
            multilingual: {value: null, valid: true, message: null},
            constraints: [],
            configuration: [],
        },
        type_validation: {
            name: { valid: !ObjectUtils.get(errors, 'type.name.0'), message: ObjectUtils.get(errors, 'type.name.0') },
            code: { valid: !ObjectUtils.get(errors, 'type.code.0'), message: ObjectUtils.get(errors, 'type.code.0') },
            icon: { valid: !ObjectUtils.get(errors, 'type.icon.0'), message: ObjectUtils.get(errors, 'type.icon.0') },
            isRoutable: { valid: !ObjectUtils.get(errors, 'type.isRoutable.0'), message: ObjectUtils.get(errors, 'type.isRoutable.0') },
            isHierarchical: { valid: !ObjectUtils.get(errors, 'type.isHierarchical.0'), message: ObjectUtils.get(errors, 'type.isHierarchical.0') }
        }
    },
    creation_mode: window.ContentBuilderLayoutBuilder.creationMode,
    is_multilingual: window.ContentBuilderLayoutBuilder.multilingual,
});

const sourceModel = window.ContentBuilderLayoutBuilder.model;
const model = reactive({
    type: {
        name: ObjectUtils.get(sourceModel, 'type.name'),
        code: ObjectUtils.get(sourceModel, 'type.code'),
        icon: ObjectUtils.get(sourceModel, 'type.icon', 'fas fa-boxes'),
        isRoutable: ObjectUtils.get(sourceModel, 'type.isRoutable', false) ? '1' : '0',
        isHierarchical: ObjectUtils.get(sourceModel, 'type.isHierarchical', false) ? '1' : '0',
        routingStrategy: ObjectUtils.get(sourceModel, 'type.routingStrategy', null),
    },
    layout: {
        sidebar: {
            sections: ObjectUtils.get(sourceModel, 'layout.sidebar.sections', [])
        },
        main: {
            sections: ObjectUtils.get(sourceModel, 'layout.main.sections', [])
        }
    }
});

const ContainerBuilder = require('./shared/ContainerBuilder.js').default;
const {
    eventDispatcher,
    options,
    translations,
    form,
    model: modelManager
} = ContainerBuilder.build(
    view,
    model,
    (name, service) => provide(name, service)
);

const generateTypeCode = () => {
    if (view.creation_mode === false) {
        return;
    }

    if (model.type.code === '') {
        view.form.code_field_changed = false;
    }

    if (view.form.code_field_changed) {
        return;
    }

    model.type.code = model.type.name.toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/is, '_');
};

onMounted(() => {
    let creationModal = document.getElementById('ctb-create-field-modal');
    view.modal.field_creator = new bootstrap.Modal(creationModal);

    creationModal.addEventListener('shown.bs.modal', function () {
        $(creationModal).find('.ctb-autofocus').focus();
    });

    view.modal.field_editor = new bootstrap.Modal(document.getElementById('ctb-edit-field-modal'));
});
</script>
