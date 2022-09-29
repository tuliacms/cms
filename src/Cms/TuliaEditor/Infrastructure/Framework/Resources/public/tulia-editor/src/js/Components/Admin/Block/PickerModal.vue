<template>
    <teleport to="#tued-modals-container">
        <div :class="{ 'modal': true, 'fade': true, 'show': blocksPicker.isOpened() }" @click.self="blocksPicker.close()" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translator.trans('newBlock') }}</h5>
                        <button type="button" class="btn-close" @click="blocksPicker.close()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0 m-0">
                        <div class="tued-block-selector">
                            <div
                                class="tued-block-item"
                                v-for="block in availableBlocks"
                                :key="block.code"
                                @click="blocksPicker.select(block.code)"
                            >
                                <div class="tued-block-item-inner">
                                    <img :src="block.icon" />
                                    <span>{{ block.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click.self="blocksPicker.close()">{{ translator.trans('cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </teleport>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');

const blocksPicker = inject('blocks.picker');
const translator = inject('translator');
const options = inject('options');

const props = defineProps(['availableBlocks', 'blockPickerData']);

const availableBlocks = computed(function () {
    let blocks = [];

    for (let i in props.availableBlocks) {
        if (0 <= options.themes.indexOf(props.availableBlocks[i].theme) || props.availableBlocks[i].theme === '*') {
            blocks.push(props.availableBlocks[i]);
        }
    }

    return blocks;
});
</script>
