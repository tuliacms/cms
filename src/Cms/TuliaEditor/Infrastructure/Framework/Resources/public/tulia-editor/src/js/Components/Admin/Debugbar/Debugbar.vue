<template>
    <div class="tued-debugbar">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#" :class="{ 'nav-link': true, 'active': activeTab === 'messenger' }" @click="activeTab = 'messenger'">Messenger</a>
            </li>
        </ul>
        <div class="tab-content">
            <div :class="{ 'tab-pane': true, 'active show': activeTab === 'messenger' }">
                <Messenger></Messenger>
            </div>
        </div>
    </div>
</template>

<script setup>
const Messenger = require('components/Admin/Debugbar/Parts/Messenger.vue').default;
const { defineProps, defineEmits, computed, inject, ref } = require('vue');

const activeTab = ref('messenger');
</script>
