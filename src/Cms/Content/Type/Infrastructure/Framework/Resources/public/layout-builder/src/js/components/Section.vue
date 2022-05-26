<template>
    <div class="ctb-section">
        <div class="ctb-section-label">
            <span class="ctb-section-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
            <input
                type="text"
                v-model="section.name.value"
                :class="{ 'ctb-section-label-input': true, 'ctb-section-label-input-has-error': ! section.name.valid }"
                data-bs-tooltip
                :title="section.name.message"
            />
            <div class="ctb-section-options">
                <span @click="$emit('section:remove', section.code)" data-bs-tooltip :title="translations.removeSection"><i class="fas fa-trash"></i></span>
            </div>
        </div>
        <div class="ctb-section-fields-container">
            <Fields
                :fields="section.fields"
                :section="section"
                group="fields"
                :parent_field="null"
            ></Fields>
        </div>
    </div>
</template>

<script>
const Fields = require('./Fields.vue').default;

export default {
    props: ['section'],
    inject: ['translations'],
    components: { Fields }
}
</script>
