<template>
    <div class="px-3 py-3">
        <div class="form-check mb-0">
            <input class="form-check-input" v-model="disableSwitchTabs" type="checkbox" value="" id="tued-disable-switch-tabs">
            <label class="form-check-label" for="tued-disable-switch-tabs">
                Disable switch tabs (always on top)
            </label>
        </div>
    </div>
    <div class="accordion accordion-flush" id="tued-debug-panes">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#tued-debug-page-messaging">
                    Messaging
                </button>
            </h2>
            <div id="tued-debug-page-messaging" class="accordion-collapse collapse show" data-bs-parent="#tued-debug-panes">
                <Messenger></Messenger>
            </div>
        </div>
    </div>
</template>
<script setup>
const Messenger = require('components/Admin/Debug/Parts/Messenger.vue').default;
const { defineProps, defineEmits, ref, inject, reactive, onMounted, watch } = require('vue');
const emits = defineEmits(['disableSwitchTabs', 'enableSwitchTabs']);
const disableSwitchTabs = ref(false);

watch(disableSwitchTabs, (newValue) => {
    if (newValue) {
        emits('disableSwitchTabs');
    } else {
        emits('enableSwitchTabs');
    }
});
</script>
