<template>
    <div>
        <draggable
            :group="group"
            :list="fields"
            v-bind="dragOptions"
            handle=".ctb-sortable-handler"
            item-key="code"
            tag="div"
            ghost-class="ctb-draggable-ghost"
            :component-data="{ class: 'ctb-sortable-fields' }"
        >
            <template #item="{element}">
                <div
                    :class="{ 'ctb-field': true, 'ctb-field-has-error': element.metadata.has_errors, 'ctb-field-has-children': element.children.length > 0 }"
                >
                    <div class="ctb-field-header">
                        <span class="ctb-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
                        <span class="ctb-field-label">
                            {{ element.name.value }}
                        </span>
                        <div class="ctb-field-options">
                            <span @click="editFieldModal.open(element.code.value)" data-bs-tooltip :title="translations.editField"><i class="fas fa-pen"></i></span>
                            <span @click="model.removeField(element.code.value)" data-bs-tooltip :title="translations.removeField"><i class="fas fa-trash"></i></span>
                        </div>
                    </div>
                    <div class="ctb-field-children" v-if="element.type.value === 'repeatable'">
                        <Fields
                            :fields="element.children"
                            :section="section"
                            :group="element.code.value + '_fields'"
                            :parent_field="element"
                        ></Fields>
                    </div>
                </div>
            </template>
        </draggable>
        <div class="ctb-field-add-btn text-center" v-if="parent_field === null || parent_field.type.value === 'repeatable'">
            <button class="ctb-button" type="button" @click="createFieldModal.open(section.code, parent_field ? parent_field.code.value : null)"><span>{{ translations.addNewField }}</span></button>
        </div>
    </div>
</template>

<script>
const draggable = require('vuedraggable');

export default {
    name: 'fields',
    props: ['fields', 'group', 'section', 'parent_field'],
    components: {
        draggable
    },
    inject: ['eventDispatcher', 'translations', 'model', 'createFieldModal', 'editFieldModal'],
    computed: {
        dragOptions() {
            return {
                animation: 200,
                disabled: false,
                ghostClass: 'ctb-draggable-ghost'
            };
        }
    }
}
</script>
