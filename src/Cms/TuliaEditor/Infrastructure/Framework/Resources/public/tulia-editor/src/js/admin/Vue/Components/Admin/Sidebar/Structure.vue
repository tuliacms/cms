<template>
    <div class="tued-structure">
        <!--<draggable group="sections" :list="sections" v-bind="dragOptions" handle=".ctb-section-sortable-handler" class="ctb-sections-container">
            <transition-group type="transition" :name="!drag ? 'flip-list' : null" class="ctb-sortable-placeholder" tag="div" :data-label="translations.addNewSection">
                <Section
                    v-for="(section, id) in sections"
                    :key="section.code"
                    :section="section"
                    :translations="translations"
                    :errors="$get(errors, id, {})"
                    @section:remove="removeSection"
                ></Section>
            </transition-group>
        </draggable>-->

        <div
            class="tued-structure-element tued-structure-element-section"
            v-for="(section, sk) in structure.sections"
            :key="'section-' + sk"
        >
            <span
                :class="{
                    'tued-label': true,
                    'tued-element-selected': selected.id === section.id,
                    'tued-element-hovered': hovered.id === section.id
                }"
                @click.stop="select('section', section)"
                @mouseenter="enter('section', section)"
                @mouseleave="leave('section', section)"
            >
                Sekcja
            </span>
            <div
                class="tued-structure-element tued-structure-element-row"
                v-for="(row, rk) in section.rows"
                :key="'row-' + rk"
            >
                <span
                    :class="{
                        'tued-label': true,
                        'tued-element-selected': selected.id === row.id,
                        'tued-element-hovered': hovered.id === row.id
                    }"
                    @click.stop="select('row', row)"
                    @mouseenter="enter('row', row)"
                    @mouseleave="leave('row', row)"
                >
                    Wiersz
                </span>
                <div
                    class="tued-structure-element tued-structure-element-column"
                    v-for="(column, ck) in row.columns"
                    :key="'column-' + ck"
                >
                    <span
                        :class="{
                            'tued-label': true,
                            'tued-element-selected': selected.id === column.id,
                            'tued-element-hovered': hovered.id === column.id
                        }"
                        @click.stop="select('column', column)"
                        @mouseenter="enter('column', column)"
                        @mouseleave="leave('column', column)"
                    >
                        Kolumna
                    </span>
                    <div
                        class="tued-structure-element tued-structure-element-block"
                        v-for="(block, bk) in column.blocks"
                        :key="'block-' + bk"
                    >
                        <span
                            :class="{
                                'tued-label': true,
                                'tued-element-selected': selected.id === block.id,
                                'tued-element-hovered': hovered.id === block.id
                            }"
                            @click.stop="select('block', block)"
                            @mouseenter="enter('block', block)"
                            @mouseleave="leave('block', block)"
                        >
                            Blok
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
    props: ['structure', 'messenger'],
    components: {
        draggable
    },
    data () {
        return {
            dragOptions: {
                animation: 200,
                group: 'sections',
                disabled: false,
                ghostClass: 'ctb-draggable-ghost'
            },
            selected: {
                id: null
            },
            hovered: {
                id: null
            }
        }
    },
    methods: {
        select: function (type, object) {
            this.messenger.send('structure.selection.select', type, object.id);
        },
        deselect: function () {
            this.messenger.send('structure.selection.deselected');
        },
        enter: function (type, object) {
            this.messenger.send('structure.hovering.enter', type, object.id);
        },
        leave: function (type, object) {
            this.messenger.send('structure.hovering.leave', type, object.id);
        }
    },
    mounted () {
        this.messenger.listen('structure.selection.selected', (type, id) => {
            this.selected.id = id;
        });
        this.messenger.listen('structure.selection.deselected', () => {
            this.selected.id = null;
        });
        this.messenger.listen('structure.hovering.hover', (type, id) => {
            this.hovered.id = id;
        });
        this.messenger.listen('structure.hovering.clear', () => {
            this.hovered.id = null;
        });
        this.messenger.listen('structure.changed', (structure) => {
            this.structure = structure;
        });
    }
};
</script>
