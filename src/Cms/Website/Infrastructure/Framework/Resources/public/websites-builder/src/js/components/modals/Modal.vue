<template>
    <Teleport to="body">
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-labelledby="" aria-hidden="true" ref="modalElement">
            <div :class="modalClassname">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <slot name="body" />
                    </div>
                    <div class="modal-footer">
                        <slot name="footer"></slot>
                    </div>
                    <div class="loader">
                        <div class="load-inner">
                            <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped lang="scss">
.modal-dialog {
    position: relative;
    z-index: 1;
}
.loader {
    position: absolute;
    z-index: 1000;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255,255,255,.5);
    display: none !important;

    .load-inner {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }

    &:hover {
        cursor: wait;
    }
}
.modal-loading {
    .loader {
        display: block !important;
    }
}
</style>

<script setup>
const { onMounted, ref, defineProps, defineExpose, computed } = require('vue');
const { Modal } = require('bootstrap');

const props = defineProps({
    title: {
        type: String,
        default: "<<Title goes here>>",
    },
    modificators: {
        type: String,
        default: 'modal-dialog-centered',
    },
});

let modalElement = ref(null);
let thisModalObj = null;

const modalClassname = computed(() => {
    return 'modal-dialog ' + props.modificators;
});

onMounted(() => {
    thisModalObj = new Modal(modalElement.value);
});
function show () {
    thisModalObj.show();
}
function hide () {
    thisModalObj.hide();
}
function showLoader () {
    modalElement.value.classList.add('modal-loading');
}
function hideLoader () {
    modalElement.value.classList.remove('modal-loading');
}
defineExpose({ show, hide, showLoader, hideLoader });
</script>
