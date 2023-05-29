<template>
    <div class="tued-structure" ref="structureContainer">
        <Section
            v-for="section in structure.sections"
            :key="section.id"
            :section="section"
            @selection-enter="(id, type) => selectionEnter(id, type)"
            @selection-leave="(id, type) => selectionLeave(id, type)"
        ></Section>

        <div class="tued-structure-new-element" @click="newBlock()">
            {{ translator.trans('newBlock') }}
        </div>

        <div
            class="tued-element-boundaries tued-element-selected-boundaries"
            :style="{
                left: selection.selected.boundaries.left + 'px',
                top: selection.selected.boundaries.top + 'px',
                width: selection.selected.boundaries.width + 'px',
                height: selection.selected.boundaries.height + 'px',
            }"
        >
            <div class="tued-node-name">{{ selection.selected.tagName }}</div>
        </div>
        <div
            class="tued-element-boundaries tued-element-hovered-boundaries"
            :style="{
                left: selection.hovered.boundaries.left + 'px',
                top: selection.hovered.boundaries.top + 'px',
                width: selection.hovered.boundaries.width + 'px',
                height: selection.hovered.boundaries.height + 'px',
            }"
        >
        </div>
        <!--<div
            class="tued-element-actions"
            ref="element-actions"
            :style="{
                width: actions.style.width + 'px',
                left: actions.style.left + 'px',
                top: actions.style.top + 'px',
            }"
        >
            <div
                class="tued-element-action"
                :title="translator.trans('selectParentElement')"
                @click="selectParentSelectable()"
                v-if="actions.activeness.selectParent"
            ><i class="fas fa-long-arrow-alt-up"></i></div>
            <div
                class="tued-element-action"
                :title="translator.trans('delete')"
                @click="deleteSelectedElement()"
                v-if="actions.activeness.delete"
            ><i class="fas fa-trash"></i></div>
        </div>-->
    </div>
</template>

<script setup>
import Section from "editor/Structure/Section.vue";
import { inject, ref } from "vue";

const selection = inject('selection.store');
const structure = inject('structure.store');
const messenger = inject('messenger');
const translator = inject('translator');
const structureContainer = ref(null);

const findNode = function (id, type) {
    let node = structureContainer.value.querySelector(`#tued-structure-${type}-${id}`);
    let interval;

    if (!node) {
        interval = setInterval(function() {
            node = structureContainer.value.querySelector(`#tued-structure-${type}-${id}`);

            if (node) {
                clearInterval(interval);
            }
        }, 10);
    }

    return node;
};

inject('selection.selectedElementBoundaries').registerHtmlNodeFinder((id, type) => findNode(id, type));
inject('selection.hoveredElementBoundaries').registerHtmlNodeFinder((id, type) => findNode(id, type));

const hoverResolver = inject('selection.hoveredElementResolver');

const selectionEnter = (id, type) => hoverResolver.enter(id, type);
const selectionLeave = () => hoverResolver.leave();
const newBlock = () => messenger.send('structure.create.block');
</script>
