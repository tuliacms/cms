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
                        <h3 class="pt-3 ps-3 mb-0">Global</h3>
                        <div class="tued-block-selector">
                            <div
                                :class="{ 'tued-block-item': true, 'd-none': block.theme !== '*' }"
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
                        <div v-for="theme in options.themes">
                            <h3 class="pt-3 ps-3 mb-0">{{ theme }}</h3>
                            <div class="tued-block-selector">
                                <div
                                    :class="{ 'tued-block-item': true, 'd-none': block.theme !== theme }"
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
const availableBlocksRaw = inject('options.blocks');

const blockPickerData = [];

const supportsTheme = block => {
    return 0 <= options.themes.indexOf(block.theme) || block.theme === '*';
};
const supportsCssFramework = block => {
    return options.css_framework === block.framework || block.framework === '*';
};

const availableBlocks = computed(function () {
    let blocks = [];

    for (let i in availableBlocksRaw) {
        if (!availableBlocksRaw[i].hasOwnProperty('theme')) {
            throw new Error(`Block ${availableBlocksRaw[i].code} does not have defined supported theme option.`);
        }
        if (!availableBlocksRaw[i].hasOwnProperty('framework')) {
            throw new Error(`Block ${availableBlocksRaw[i].code} does not have defined supported CSS framework option.`);
        }

        if (
            supportsTheme(availableBlocksRaw[i])
            && supportsCssFramework(availableBlocksRaw[i])
        ) {
            blocks.push(availableBlocksRaw[i]);
        }
    }

    return blocks;
});
</script>
