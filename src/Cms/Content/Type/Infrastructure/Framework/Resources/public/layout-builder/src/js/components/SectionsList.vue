<template>
    <div>
        <draggable
            group="sections"
            :list="sections"
            v-bind="dragOptions"
            handle=".ctb-section-sortable-handler"
            item-key="code"
            tag="div"
            :component-data="{ class: 'ctb-sections-container' }"
        >
            <template #item="{element}">
                <Section
                    :key="element.code"
                    :section="element"
                    errors="ObjectUtils.get(errors, id, {})"
                    @section:remove="removeSection()"
                ></Section>
            </template>
        </draggable>
        <div class="text-center">
            <div class="ctb-new-section-button" @click="addSection()">
                <i class="fa fa-plus"></i>
                {{ translations.addNewSection }}
            </div>
        </div>
    </div>
</template>

<script>
const Section = require('./Section.vue').default;
const draggable = require('vuedraggable');
const ObjectUtils = require('./../shared/ObjectUtils.js').default;

export default {
    props: ['sections', 'errors'],
    components: {
        Section,
        draggable
    },
    inject: ['translations'],
    data () {
        return {
            ObjectUtils: ObjectUtils
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: 'sections',
                disabled: false,
                ghostClass: 'ctb-draggable-ghost'
            };
        }
    },
    methods: {
        addSection: function () {
            this.sections.push({
                code: _.uniqueId('section_'),
                name: {
                    value: 'New section...',
                    valid: true,
                    message: null
                },
                fields: []
            });
        },
        removeSection: function (code) {
            Tulia.Confirmation.warning().then((result) => {
                if (result.value) {
                    for (let i in this.sections) {
                        if (this.sections[i].code === code) {
                            this.sections.splice(i, 1);
                        }
                    }
                }
            });
        }
    }
}
</script>
